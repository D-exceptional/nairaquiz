<?php

require 'session.php'; // Assumes $userID and $email are defined
require 'conn.php';

header('Content-Type: application/json');
date_default_timezone_set('Africa/Lagos');

// Retrieve POST data safely
$point   = isset($_POST['point']) ? (int)$_POST['point'] : null;
$session = isset($_POST['session']) ? trim($_POST['session']) : null;
$date    = date('Y-m-d H:i:s');
$type    = 1;
$stake = 500; // Formerly 1000 (1K)

// Default response
$response = ['Info' => 'Some fields are empty', 'data' => ['amount' => 0]];

// Basic validation
if (empty($userID) || is_null($point) || empty($session)) {
    $response['data']['amount'] = getWalletAmount($conn, $userID);
    echo json_encode($response, JSON_FORCE_OBJECT);
    exit;
}

// Check if the session has reached the max number of players
$stmt = $conn->prepare("SELECT COUNT(*) FROM session_players WHERE session_name = ?");
$stmt->bind_param("s", $session);
$stmt->execute();
$stmt->bind_result($total_players);
$stmt->fetch();
$stmt->close();

if ($total_players >= 1000) {
    $response['Info'] = "Game slots filled. Wait for next session";
    $response['data']['amount'] = getWalletAmount($conn, $userID);
    echo json_encode($response, JSON_FORCE_OBJECT);
    exit;
}

// Check if user already played this session
$stmt = $conn->prepare("SELECT 1 FROM session_players WHERE session_name = ? AND userID = ?");
$stmt->bind_param("si", $session, $userID);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    $response['Info'] = "You have already played in this session. Wait for next session";
    $response['data']['amount'] = getWalletAmount($conn, $userID);
    echo json_encode($response, JSON_FORCE_OBJECT);
    exit;
}
$stmt->close();

// Insert new play session
$stmt = $conn->prepare("INSERT INTO session_players (user_point, play_date, session_name, userID) VALUES (?, ?, ?, ?)");
$stmt->bind_param("issi", $point, $date, $session, $userID);

if ($stmt->execute()) {
    $stmt->close();

    // Deduct wallet balance
    $wallet_amount = getWalletAmount($conn, $userID);
    $new_balance = $wallet_amount > 0 ? max(0, $wallet_amount - 500) : 0.1;

    $stmt = $conn->prepare("UPDATE wallet SET wallet_amount = ? WHERE userID = ?");
    $stmt->bind_param("di", $new_balance, $userID);
    $stmt->execute();
    $stmt->close();

    // Log trial
    $stmt = $conn->prepare("INSERT INTO quiz_trials (trial_type, trial_stake, trial_points, trial_date, userID) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("idisi", $type, $stake, $point, $date, $userID);
    $stmt->execute();
    $stmt->close();

    $response['Info'] = "Trial saved successfully";
    $response['data']['amount'] = getWalletAmount($conn, $userID);
} else {
    $stmt->close();
    $response['Info'] = "Something went wrong while saving your game play.";
    $response['data']['amount'] = getWalletAmount($conn, $userID);
}

echo json_encode($response, JSON_FORCE_OBJECT);
mysqli_close($conn);

// ───────────────
// Helper Function
// ───────────────
function getWalletAmount(mysqli $conn, int $userID): float {
    $stmt = $conn->prepare("SELECT wallet_amount FROM wallet WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($amount);
    $stmt->fetch();
    $stmt->close();
    return $amount ?? 0;
}
?>
