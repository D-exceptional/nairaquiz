<?php 

// Database connection file
require 'conn.php';

// Get mailer file
include "mailer.php";

function checkTransaction($reference, $currency) {
    global $conn;
    
    // Function to get user details
    function getUserDetails($email) {
        global $conn;
        $stmt = $conn->prepare("SELECT userID, fullname FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // Function to update wallet and send notification
    function updateWallet($userID, $fund_amount, $currency, $fullname) {
        global $conn;
        $stmt = $conn->prepare("SELECT wallet_amount FROM wallet WHERE userID = ?");
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        $wallet = $stmt->get_result()->fetch_assoc();
        
        $new_balance = $wallet['wallet_amount'] + $fund_amount;
        $stmt = $conn->prepare("UPDATE wallet SET wallet_amount = ? WHERE userID = ?");
        $stmt->bind_param("ds", $new_balance, $userID);
        $stmt->execute();
        
        return $new_balance;
    }

    // Check transaction status
    $stmt = $conn->prepare("SELECT * FROM wallet_fund WHERE fund_txref = ?");
    $stmt->bind_param("s", $reference);
    $stmt->execute();
    $status_check = $stmt->get_result();
    
    if ($status_check->num_rows > 0) {
        $row = $status_check->fetch_assoc();
        if ($row['fund_status'] === "Pending") {
            // Handle pending transaction
            $userID = $row['userID'];
            $fund_amount = $row['fund_amount'];
            $fullname = getUserDetailsByID($userID)['fullname'];

            $new_balance = updateWallet($userID, $fund_amount, $currency, $fullname);

            // Update transaction status to completed
            $stmt = $conn->prepare("UPDATE wallet_fund SET fund_status = 'Completed' WHERE fund_txref = ?");
            $stmt->bind_param("s", $reference);
            $stmt->execute();

            // Send email
            sendFundingEmail($fullname, $currency, $fund_amount);

            // Create notification
            createNotification($fullname, $currency, $fund_amount, $email);
        }
    } else {
        // Verify with Paystack
        verifyWithPaystack($reference);
    }
}

function verifyWithPaystack($reference) {
    global $conn, $secretKey;

    $url = "https://api.paystack.co/transaction/verify/" . $reference;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $secretKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if ($err = curl_error($ch)) {
        return ['error' => 'Curl error: ' . $err];
    }

    $transaction = json_decode($response);
    if (!$transaction->data || !$transaction->data->status) {
        return ['error' => 'Paystack API error'];
    }

    handleTransactionData($transaction);
}

function handleTransactionData($transaction) {
    global $conn;

    $amount = $transaction->data->amount / 100; // Convert to original amount
    $date = $transaction->data->paid_at;
    $status = $transaction->data->status;
    $email = $transaction->customer->email;

    $userDetails = getUserDetails($email);
    if (!$userDetails) return; // User not found

    $userID = $userDetails['userID'];
    $fullname = $userDetails['fullname'];
    $walletID = getWalletID($userID);

    if ($status === "success") {
        completeTransaction($userID, $walletID, $amount, $date);
    } else {
        // Handle pending status
        insertPendingTransaction($userID, $walletID, $amount, $date);
    }
}

function completeTransaction($userID, $walletID, $amount, $date) {
    global $conn;
    // Insert into wallet_fund
    $stmt = $conn->prepare("INSERT INTO wallet_fund (fund_amount, fund_date, fund_status, fund_txref, userID, walletID) VALUES (?, ?, 'Completed', ?, ?, ?)");
    $stmt->bind_param("dssis", $amount, $date, $reference, $userID, $walletID);
    $stmt->execute();

    $new_balance = updateWallet($userID, $amount, $currency, $fullname);
    
    // Send email
    sendFundingEmail($fullname, $currency, $amount);

    // Create notification
    createNotification($fullname, $currency, $amount, $email);
}

function sendFundingEmail($fullname, $currency, $fund_amount) {
    $top_up_amount = $currency . number_format($fund_amount, 2, '.', ',');
    $link = 'https://nairaquiz.com/login';
    $text = 'Login';
    $subject = "Successful Wallet Funding";
    $message = "
        Hi <b>$fullname</b>, <br>
        You have successfully funded <b>$top_up_amount</b> to your wallet. <br>
        You can now take more live quiz via your dashboard. <br>
        Voila!
        <br>
        <br>
        <a href='$link' target='_blank'><b>$text</b></a>
    ";
    send_email($subject, $email, $message);
}

function createNotification($fullname, $currency, $fund_amount, $email) {
    global $conn;
    $top_up_amount = $currency . number_format($fund_amount, 2, '.', ',');
    $notification_title = 'Funded Wallet';
    $notification_details = "Hi, $fullname! <br> You have successfully funded your wallet with <b>$top_up_amount</b> <br> You can now take more quizzes!";
    $notification_date = date('Y-m-d H:i:s');
    $notification_status = 'Unseen';

    $stmt = $conn->prepare("INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_receiver, notification_date, notification_status) VALUES (?, ?, 'wallet_fund', ?, ?, ?)");
    $stmt->bind_param("sssss", $notification_title, $notification_details, $email, $notification_date, $notification_status);
    $stmt->execute();
}

function getWalletID($userID) {
    global $conn;
    $stmt = $conn->prepare("SELECT walletID FROM wallet WHERE userID = ?");
    $stmt->bind_param("s", $userID);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['walletID'];
}

?>
