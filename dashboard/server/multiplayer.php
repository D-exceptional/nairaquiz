<?php
// ─────────────────────────────────────────────
// 1. REQUIRED FILES AND INITIAL SETUP
// ─────────────────────────────────────────────
require 'conn.php';
require 'session.php'; // Assumes $userID is defined and valid
require 'format_number.php';

header('Content-Type: application/json');
date_default_timezone_set('Africa/Lagos');

// Default response
$response = ['Info' => 'Invalid request type', 'data' => []];

// ─────────────────────────────────────────────
// 2. LOAD CACHE FROM FILE
// ─────────────────────────────────────────────
$cache = getCache();
if (!$cache) {
    echo json_encode(['Info' => 'No active session found', 'data' => []]);
    exit;
}

$question = $cache['question'] ?? null;
$session  = $cache['session'] ?? null;
$start    = $cache['start'] ?? null;
$end      = $cache['end'] ?? null;
$next  = $cache['next'] ?? null;

if (!$question || !$session || !$start || !$end || !$next) {
    echo json_encode(['Info' => 'Invalid game session', 'data' => []]);
    exit;
}

// ─────────────────────────────────────────────
// 3. TIME VALIDATION — Sync with Global Timer
// ─────────────────────────────────────────────
$currentTime = new DateTime();
$startTime   = new DateTime($start);
$endTime     = new DateTime($end);

// If current time is before session window
if ($currentTime < $startTime) {
    echo json_encode([
        'Info' => 'Next session starts soon',
        'data' => [
            'start' => $start,
        ]
    ]);
    exit;
}

// If session has expired (after answer window)
if ($currentTime >= $endTime) {
    echo json_encode([
        'Info' => 'The session has expired',
        'data' => [
            'next' => $next,
        ]
    ]);
    exit;
}

// ─────────────────────────────────────────────
// 4. CHECK IF USER HAS ALREADY PLAYED
// ─────────────────────────────────────────────
if (hasPlayedInSession($conn, $session, $userID)) {
    echo json_encode([
        'Info' => 'You have already played in this session',
        'data' => [
            'next' => $next,
        ]
    ]);
    exit;
}

// 5. Prepare Packet
$isAdmin = in_array(intval($userID), [3, 7]);
$sessionPacket = $isAdmin ? $question['correctAnswer'] : '';

// ─────────────────────────────────────────────
// 6. RETURN QUESTION
// ─────────────────────────────────────────────
unset($question['correctAnswer']);
$response = [
    'Info' => 'You have successfully joined this session',
    'data' => [
        'question' => $question,
        'session'  => $session,
        'end'      => $end,
        'next'   => $next,
        'total'   => getSessionPlayers($conn, $session),
        'packet'   => $sessionPacket,
    ]
];

mysqli_close($conn);
echo json_encode($response);


// ─────────────────────────────────────────────
// 6. HELPER FUNCTIONS
// ─────────────────────────────────────────────

function getCache() {
    $cacheFile = '../../cache.json';
    if (file_exists($cacheFile)) {
        $content = file_get_contents($cacheFile);
        $cacheData = json_decode($content, true);
        return is_array($cacheData) ? $cacheData : null;
    }
    return null;
}

function hasPlayedInSession($conn, $session, $userID) {
    $stmt = $conn->prepare("SELECT 1 FROM session_players WHERE session_name = ? AND userID = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return false;
    }
    $stmt->bind_param("si", $session, $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $played = ($result && $result->num_rows > 0);
    $stmt->close();
    return $played;
}

function getSessionPlayers($conn, $session) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM session_players WHERE session_name = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return 0;
    }

    $stmt->bind_param("s", $session);
    $stmt->execute();
    $result = $stmt->get_result();

    $count = 0;
    if ($result) {
        $row = $result->fetch_assoc();
        $count = (int) $row['total'];
    }

    $stmt->close();
    return format_number($count, 1);
}
?>
