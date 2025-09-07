<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set the time zone
date_default_timezone_set('Africa/Lagos');

// Log start
file_put_contents(__DIR__ . "/cronlog.txt", "Cron started: " . date("Y-m-d H:i:s") . "\n", FILE_APPEND);

// DB Connection
require 'assets/server/conn.php';
if (!$conn || $conn->connect_error) {
    file_put_contents(__DIR__ . "/cronlog.txt", "DB connection failed: " . $conn->connect_error . "\n", FILE_APPEND);
    die("DB connection failed");
}

// Cache file path
$cacheFile = __DIR__ . '/cache.json';

// Generate unique session code
function generateSessionCode($conn) {
    do {
        $data = uniqid('', true) . random_bytes(8);
        $hash = substr(hash('sha256', $data), 0, 16);
        $sessionName = 'Session_' . $hash;

        $stmt = $conn->prepare("SELECT COUNT(*) FROM session_game WHERE session_name = ?");
        if (!$stmt) return null;

        $stmt->bind_param("s", $sessionName);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    } while ($count > 0);

    return $sessionName;
}

// Get a random question
function getRandomQuestion($conn) {
    $stmt = $conn->prepare("SELECT question_details, option_one, option_two, option_three, option_four, correct_option FROM questions ORDER BY RAND() LIMIT 1");
    if (!$stmt) return null;

    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if (!$result) return null;

    return [
        'question' => $result['question_details'],
        'answers' => [
            'a' => $result['option_one'],
            'b' => $result['option_two'],
            'c' => $result['option_three'],
            'd' => $result['option_four'],
        ],
        'correctAnswer' => strtolower(trim($result['correct_option'])),
    ];
}

// Save to DB
function saveData($conn, $session_name, $question, $time) {
    $status = 'Pending';
    $stmt = $conn->prepare("INSERT INTO session_game (session_name, session_question, session_answer, session_date, session_status) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) return;

    $stmt->bind_param("sssss", $session_name, $question['question'], $question['correctAnswer'], $time, $status);
    $stmt->execute();
    $stmt->close();
}

// Main cache logic
function updateCache($conn) {
    global $cacheFile;

    $session_name = generateSessionCode($conn);
    if (!$session_name) {
        file_put_contents(__DIR__ . "/cronlog.txt", "Failed to generate session name\n", FILE_APPEND);
        return;
    }

    $question = getRandomQuestion($conn);
    if (!$question) {
        file_put_contents(__DIR__ . "/cronlog.txt", "No valid question found\n", FILE_APPEND);
        return;
    }

    // Align time to current 5-minute block
    $now = new DateTime();
    $minute = floor((int)$now->format('i') / 5) * 5;
    $start_time = clone $now;
    $start_time->setTime((int)$now->format('H'), $minute, 0);

    $validSeconds = 20;
    $end_time = clone $start_time;
    $end_time->modify("+$validSeconds seconds");

    $next_session = clone $start_time;
    $next_session->modify('+5 minutes');

    $cacheData = [
        'question' => $question,
        'session' => $session_name,
        'start' => $start_time->format('Y-m-d H:i:s'),
        'end' => $end_time->format('Y-m-d H:i:s'),
        'next' => $next_session->format('Y-m-d H:i:s'),
    ];

    if (file_put_contents($cacheFile, json_encode($cacheData)) === false) {
        file_put_contents(__DIR__ . "/cronlog.txt", "Failed to write to cache.json\n", FILE_APPEND);
    } else {
        file_put_contents(__DIR__ . "/cronlog.txt", "Cache updated successfully: " . $session_name . "\n", FILE_APPEND);
    }

    saveData($conn, $session_name, $question, $start_time->format('Y-m-d H:i:s'));
}

// Run cache update
updateCache($conn);

// Close DB connection
$conn->close();

// Log end
file_put_contents(__DIR__ . "/cronlog.txt", "Cron completed: " . date("Y-m-d H:i:s") . "\n\n", FILE_APPEND);

?>