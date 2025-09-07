<?php

date_default_timezone_set("Africa/Lagos");

require 'conn.php';
require "mailer.php";

$userID = mysqli_real_escape_string($conn, $_POST['id']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$amount = mysqli_real_escape_string($conn, $_POST['amount']);  
$currency = mysqli_real_escape_string($conn, $_POST['currency']);
$reference = mysqli_real_escape_string($conn, $_POST['reference']);
$db_amount = $amount / 100;
$date = date('Y-m-d H:i:s');

if (empty($userID) || empty($email) || empty($amount) || empty($currency) || empty($reference)) {
    echo json_encode(['Info' => 'Missing parameters', 'details' => ['error' => 'Some parameters are missing from request data']]);
    exit();
}

$walletID = getOrCreateWallet($userID, $currency);

if ($walletID) {
    createPaymentRecord($userID, $db_amount, $date, $reference, $walletID);
    updateWalletAndNotify($userID, $reference, $email, $db_amount);
    echo json_encode(['Info' => 'Payment successful', 'details' => ['message' => "Your payment of " . "$currency" . number_format($db_amount, 2, '.', ',') . " was successful. Kindly check your mailbox for more info."]]);
} else {
    echo json_encode(['Info' => 'Wallet not found', 'details' => ['error' => 'No wallet was found for user']]);
}

mysqli_close($conn);
exit();

function getOrCreateWallet($userID, $currency) {
    global $conn;
    
    $sql = mysqli_query($conn, "SELECT * FROM wallet WHERE userID = '$userID'");
    
    if (mysqli_num_rows($sql) === 0) {
        $wallet_amount = 0.1;
        $wallet_status = "Active";
        $account_number = "0000000000"; // Consider changing to a meaningful default
        $bank = "null";
        $recipient_code = "Not available";
        
        $create_wallet = mysqli_query($conn, "INSERT INTO wallet (wallet_amount, wallet_currency, wallet_status, account_number, bank, bank_code, recipient_code, userID) VALUES ('$wallet_amount', '$currency', '$wallet_status', '$account_number', '$bank', 'null', '$recipient_code', '$userID')");
        
        if ($create_wallet) {
            return mysqli_insert_id($conn); // Return the new wallet ID
        }
    } else {
        $row = mysqli_fetch_assoc($sql);
        return $row['walletID']; // Existing wallet ID
    }
    return null;
}

function createPaymentRecord($userID, $db_amount, $date, $reference, $walletID) {
    global $conn;
    mysqli_query($conn, "INSERT INTO wallet_fund (fund_amount, fund_date, fund_status, fund_txref, userID, walletID) VALUES ('$db_amount', '$date', 'Pending', '$reference', '$userID', '$walletID')");
}

function updateWalletAndNotify($userID, $reference, $email, $db_amount) {
    global $conn;

    $sql = mysqli_query($conn, "SELECT fund_amount, userID FROM wallet_fund WHERE fund_txref = '$reference'");
    $row = mysqli_fetch_assoc($sql);
    
    $userID = $row['userID'];
    $fund_amount = $row['fund_amount'];

    $sql = mysqli_query($conn, "SELECT wallet_amount FROM wallet WHERE userID = '$userID'");
    $row = mysqli_fetch_assoc($sql);
    
    $wallet_amount = $row['wallet_amount'];
    $new_balance = $wallet_amount + $fund_amount;

    mysqli_query($conn, "UPDATE wallet SET wallet_amount = '$new_balance' WHERE userID = '$userID'");
    mysqli_query($conn, "UPDATE wallet_fund SET fund_status = 'Completed' WHERE fund_txref = '$reference'");
    
    $fullname = getUserFullName($userID);
    sendFundingEmail($fullname, $email, $db_amount);
    createNotification($fullname, $email, $db_amount);
}

function getUserFullName($userID) {
    global $conn;
    $sql = mysqli_query($conn, "SELECT fullname FROM users WHERE userID = '$userID'");
    $row = mysqli_fetch_assoc($sql);
    return $row['fullname'];
}

function sendFundingEmail($fullname, $email, $db_amount) {
    $link = 'https://nairaquiz.com/login';
    $subject = "Successful Wallet Funding";
    $message = "
        Hi <b>$fullname</b>, <br>
        You have successfully funded <b>" . "$currency" . number_format($db_amount, 2, '.', ',') . "</b> to your wallet. <br>
        You can now take more live quiz via your dashboard. <br>
        Voila!<br><br>
        <a href='$link' target='_blank'><b>Login</b></a>
    ";
    send_email($subject, $email, $message);
}

function createNotification($fullname, $email, $db_amount) {
    global $conn;

    $notification_title = 'Funded wallet';
    $notification_details = "Hi, $fullname! <br> You have successfully funded your wallet with <b>" . "$currency" . number_format($db_amount, 2, '.', ',') . "</b> <br>You can now take more quizzes!";
    $notification_type = 'wallet_fund';
    $notification_date = date('Y-m-d H:i:s');
    $notification_status = 'Unseen';

    mysqli_query($conn, "INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_receiver, notification_date, notification_status) VALUES ('$notification_title', '$notification_details', '$notification_type', '$email', '$notification_date', '$notification_status')");
}

?>
