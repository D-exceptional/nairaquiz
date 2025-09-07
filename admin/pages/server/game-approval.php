<?php

// Set response header
header('Content-Type: application/json');

// Set timezone
date_default_timezone_set("Africa/Lagos");

// Helper files
require 'session.php';
require "mailer.php";

// Start application
$data = [];
$response = [];

$fullname = $_POST['name'] ?? '';
$email    = $_POST['email'] ?? '';
$amount   = $_POST['amount'] ?? '';
$currency = $_POST['currency'] ?? '';
$reference = $_POST['reference'] ?? '';
$date = date('m/d/Y H:i');
$userID = getUserID($conn, $email);

if (empty($fullname) || empty($email) || empty($amount) || empty($currency) || empty($reference)) {
    echo json_encode(['Info' => 'Missing parameters', 'details' => ['error' => 'Some parameters are missing from request data']]);
    exit();
}

$walletID = getOrCreateWallet($conn, $userID, $currency);

if ($walletID) {
    updateWalletAndNotify($conn, $userID, $reference, $email, $amount, $fullname, $currency);
    $response = [
        'Info' => 'Payment successful',
        'details' => ['message' => "Your payment of $currency" . number_format($amount, 2) . " was successful. Kindly check your mailbox for more info."]
    ];
} else {
    $response = [
        'Info' => 'Wallet not found',
        'details' => ['error' => 'No wallet was found for user']
    ];
}

echo json_encode($response);
mysqli_close($conn);
exit();

// Create wallet if not exists
function getOrCreateWallet($conn, $userID, $currency) {
    $stmt = $conn->prepare("SELECT walletID FROM wallet WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($walletID);
    if ($stmt->fetch()) {
        $stmt->close();
        return $walletID;
    }
    $stmt->close();

    $wallet_amount = 0;
    $wallet_status = "Active";
    $account_number = "0000000000";
    $bank = "null";
    $bank_code = "null";
    $recipient_code = "Not available";

    $stmt = $conn->prepare("INSERT INTO wallet (wallet_amount, wallet_currency, wallet_status, account_number, bank, bank_code, recipient_code, userID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("dssssssi", $wallet_amount, $currency, $wallet_status, $account_number, $bank, $bank_code, $recipient_code, $userID);
    if ($stmt->execute()) {
        $walletID = $stmt->insert_id;
        $stmt->close();
        return $walletID;
    }
    $stmt->close();
    return null;
}

// Update wallet balance and send notifications
function updateWalletAndNotify($conn, $userID, $reference, $email, $amount, $fullname, $currency) {
    $stmt = $conn->prepare("SELECT fund_amount FROM wallet_fund WHERE fund_txref = ?");
    $stmt->bind_param("s", $reference);
    $stmt->execute();
    $stmt->bind_result($fund_amount);
    if (!$stmt->fetch()) {
        $stmt->close();
        return;
    }
    $stmt->close();

    // Get wallet balance
    $stmt = $conn->prepare("SELECT wallet_amount FROM wallet WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($wallet_amount);
    $stmt->fetch();
    $stmt->close();

    $new_balance = $wallet_amount + $fund_amount;

    // Update wallet
    $stmt = $conn->prepare("UPDATE wallet SET wallet_amount = ? WHERE userID = ?");
    $stmt->bind_param("di", $new_balance, $userID);
    $stmt->execute();
    $stmt->close();

    // Update fund status
    $status = "Completed";
    $stmt = $conn->prepare("UPDATE wallet_fund SET fund_status = ? WHERE fund_txref = ?");
    $stmt->bind_param("ss", $status, $reference);
    $stmt->execute();
    $stmt->close();

    sendFundingEmail($fullname, $email, $amount, $currency);
    createNotification($conn, $fullname, $email, $amount, $currency);
}

// Send confirmation email
function sendFundingEmail($fullname, $email, $amount, $currency) {
    $link = 'https://nairaquiz.com/login';
    $subject = "Successful Wallet Funding";
    $message = "
        Hi <b>$fullname</b>, <br>
        You have successfully funded <b>$currency" . number_format($amount, 2) . "</b> to your wallet. <br>
        You can now take more live quizzes via your dashboard.<br><br>
        <a href='$link' target='_blank'><b>Login</b></a>
    ";
    send_email($subject, $email, $message);
}

// Create in-app notification
function createNotification($conn, $fullname, $email, $amount, $currency) {
    $title = 'Funded wallet';
    $details = "Hi, $fullname! <br> You have successfully funded your wallet with <b>$currency" . number_format($amount, 2) . "</b>. <br>You can now take more quizzes!";
    $type = 'wallet_fund';
    $date = date('Y-m-d H:i:s');
    $status = 'Unseen';

    $stmt = $conn->prepare("INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_receiver, notification_date, notification_status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $title, $details, $type, $email, $date, $status);
    $stmt->execute();
    $stmt->close();
}

// Get userID
function getUserID($conn, $email) {
    $userID = null;

    if ($stmt = $conn->prepare("SELECT userID FROM users WHERE email = ?")) {
        $stmt->bind_param('s', $email);
        if ($stmt->execute()) {
            $stmt->bind_result($userID);
            if (!$stmt->fetch()) {
                $userID = null; // Explicitly set if no row was found
            }
        } else {
            error_log("Execution failed: " . $stmt->error);
        }
        $stmt->close();
    } else {
        error_log("Prepare failed: " . $conn->error);
    }

    return $userID;
}

?>
