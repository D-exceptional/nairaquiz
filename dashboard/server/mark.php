<?php
// ─────────────────────────────────────────────
// 1. REQUIRED FILES AND INITIAL SETUP
// ─────────────────────────────────────────────
require 'conn.php';

header('Content-Type: application/json');
date_default_timezone_set('Africa/Lagos');

// Default response
$response = [];

// ─────────────────────────────────────────────
// 2. RETRIEVE DATA
// ─────────────────────────────────────────────
$userAnswer = mysqli_real_escape_string($conn, $_POST['answer']);
$userSession = mysqli_real_escape_string($conn, $_POST['session']);

if(!isset($userAnswer) || empty($userAnswer) || !isset($userSession) || empty($userSession)){
    echo json_encode(['status' => 'error', 'message' => 'Empty or invalid data', 'data' => []]);
    exit;
}

// ─────────────────────────────────────────────
// 3. LOAD CACHE FROM FILE
// ─────────────────────────────────────────────
$cache = getCache();
if (!$cache) {
    echo json_encode(['status' => 'error', 'message' => 'No active session found', 'data' => []]);
    exit;
}

$cacheQuestion = $cache['question'] ?? null;
$cacheSession  = $cache['session'] ?? null;

if (!$cacheQuestion || !$cacheSession) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid game session', 'data' => []]);
    exit;
}

// ─────────────────────────────────────────────
// 4. PROCESS REQUEST
// ─────────────────────────────────────────────
$correctAnswer = $cacheQuestion['correctAnswer'];

if ($userAnswer === $correctAnswer) {
   $response = [
        'status' => 'success',
        'message' => 'You have correctly answered the question for this session',
        'data' => [
            'point' => 1,
        ]
    ];
} else {
    $response = [
        'status' => 'success',
        'message' => 'You have wrongly answered the question for this session',
        'data' => [
            'point' => 0,
        ]
    ];
}

// ─────────────────────────────────────────────
// 5. RETURN QUESTION
// ─────────────────────────────────────────────
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
?>
