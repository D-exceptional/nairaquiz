<?php

header('Content-Type: application/json');
require 'session.php';
require 'mailer.php';
date_default_timezone_set("Africa/Lagos");

$response = ['Info' => 'Invalid request'];

$sessionID = $_POST['id'] ?? null;
$session_name = $_POST['name'] ?? null;

// Validate input
if (!$session_name || !$sessionID) {
    echo json_encode(['Info' => 'Session ID or name missing'], JSON_FORCE_OBJECT);
    exit;
}

processWinners($session_name);


// ──────────────────────────────
// Main Game Processing Function
// ──────────────────────────────
function processWinners($session_name)
{
    global $conn;

    $winners = [];
    $currency = 'NGN';

    // Update session status
    updateStatus($session_name);

    // Fetch all players in the session
    $stmt = $conn->prepare("SELECT userID, user_point FROM session_players WHERE session_name = ?");
    $stmt->bind_param("s", $session_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $players = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $total_players = count($players);
    if ($total_players === 0) {
        return respond("No player engaged in this game session");
    }

    // Determine winners
    foreach ($players as $player) {
        if ((int)$player['user_point'] === 1) {
            $winners[] = $player['userID'];
        }
    }

    $total_winners = count($winners);
    if ($total_winners === 0) {
        return respond("There is no winner in this game session");
    }

    // Calculate reward
    if ($total_winners === $total_players) {
        // Everyone wins: each gets 1000 to main wallet
        $reward = 1000;
        $formatted_amount = $currency . number_format($reward, 2, '.', ',');

        foreach ($players as $player) {
            $userID = (int)$player['userID'];
            updateWalletBalance($userID, $reward);
            $user_data = getUserData($userID);

            if ($user_data) {
                createNotification($user_data['fullname'], $user_data['email'], $formatted_amount, 'wallet_fund', $session_name);
                send_email("Wallet Topup", $user_data['email'], "Hello, {$user_data['fullname']} <br> Your wallet has been credited with <b>$formatted_amount</b> for winning in the game session:  <b>$session_name</b>.");
            }
        }
    } else {
        // Split 70% of total pool to winners (goes to savings wallet)
        $total_amount = $total_players * 1000;
        $player_percentage = ($total_amount * 0.70) / $total_winners;
        $formatted_amount = $currency . number_format($player_percentage, 2, '.', ',');

        foreach ($winners as $userID) {
            updateWalletBalanceInSavings($userID, $player_percentage);
            $user_data = getUserData($userID);

            if ($user_data) {
                createNotification($user_data['fullname'], $user_data['email'], $formatted_amount, 'withdrawal_wallet_fund', $session_name);
                send_email("Withdrawal Wallet Topup", $user_data['email'], "Hello, {$user_data['fullname']} <br> Your withdrawal wallet has been credited with <b>$formatted_amount</b> for winning in the game session:  <b>$session_name</b>.");
            }
        }
    }

    return respond("Game session finalized successfully");
}


// ──────────────────────────────
// Helper: Final response and close
// ──────────────────────────────
function respond($message)
{
    global $conn;
    echo json_encode(['Info' => $message], JSON_FORCE_OBJECT);
    mysqli_close($conn);
    exit;
}


// ──────────────────────────────
// Helper: Update session status
// ──────────────────────────────
function updateStatus($name)
{
    global $conn;
    $status = 'Completed';
    $stmt = $conn->prepare("UPDATE session_game SET session_status = ? WHERE session_name = ?");
    $stmt->bind_param("ss", $status, $name);
    if (!$stmt->execute()) {
        error_log("Failed to update session status for $name: " . $stmt->error);
    }
    $stmt->close();
}


// ──────────────────────────────
// Helper: Fetch user data
// ──────────────────────────────
function getUserData($userID)
{
    global $conn;
    $stmt = $conn->prepare("SELECT fullname, email FROM users WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
    return $data;
}


// ──────────────────────────────
// Helper: Update main wallet
// ──────────────────────────────
function updateWalletBalance($userID, $amount)
{
    global $conn;
    $stmt = $conn->prepare("UPDATE wallet SET wallet_amount = wallet_amount + ? WHERE userID = ?");
    $stmt->bind_param("di", $amount, $userID);
    if (!$stmt->execute()) {
        error_log("Failed to update wallet for user $userID: " . $stmt->error);
    }
    $stmt->close();
}


// ──────────────────────────────
// Helper: Update savings wallet
// ──────────────────────────────
function updateWalletBalanceInSavings($userID, $amount)
{
    global $conn;
    $stmt = $conn->prepare("UPDATE wallet_savings SET wallet_amount = wallet_amount + ? WHERE userID = ?");
    $stmt->bind_param("di", $amount, $userID);
    if (!$stmt->execute()) {
        error_log("Failed to update savings for user $userID: " . $stmt->error);
    }
    $stmt->close();
}


// ──────────────────────────────
// Helper: Create notification
// ──────────────────────────────
function createNotification($fullname, $email, $amount, $type, $game)
{
    global $conn;

    $title = 'Funded wallet';
    $details = "Hi, $fullname! <br> NairaQuiz has successfully credited your ";
    $details .= ($type === 'withdrawal_wallet_fund') ? 'withdrawal wallet' : 'wallet';
    $details .= " balance with <b>$amount</b> for winning in the game session: <b>$game</b>";

    $date = date('Y-m-d H:i:s');
    $status = 'Unseen';

    $stmt = $conn->prepare("
        INSERT INTO general_notifications 
        (notification_title, notification_details, notification_type, notification_receiver, notification_date, notification_status) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssssss", $title, $details, $type, $email, $date, $status);

    if (!$stmt->execute()) {
        error_log("Failed to create notification for $email: " . $stmt->error);
    }

    $stmt->close();
}
?>
