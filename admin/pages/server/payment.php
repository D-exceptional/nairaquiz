<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conn.php';
require 'mailer.php';

date_default_timezone_set("Africa/Lagos");

// Initialize admin emails
$admin_emails = ['chukwuebukaokeke09@gmail.com', 'krankstephen20@gmail.com'];

function sanitizeData($data) {
    return addslashes(strip_tags(trim($data)));
}

function handleErrors($error_message) {
    error_log("Error: " . $error_message . "\n", 3, 'error_log');
    header('Content-Type: application/json');
    echo json_encode(['Info' => $error_message], JSON_FORCE_OBJECT);
    exit();
}

function handleSuccess($success_message) {
    header('Content-Type: application/json');
    echo json_encode(['Info' => $success_message], JSON_FORCE_OBJECT);
    exit();
}

function initiateTransfer($array) {
    global $conn;

    foreach (array_chunk($array, 10) as $batch) {
        foreach ($batch as $item) {
            $item = array_map('sanitizeData', $item);

            // Update withdrawal status
            $stmt = $conn->prepare("UPDATE withdrawals SET payment_status = ? WHERE payment_txref = ?");
            $status = 'Completed';
            $stmt->bind_param("ss", $status, $item['reference']);
            $stmt->execute();

            // Send email
            $subject = "NairaQuiz Payout";
            $link = "#";
            $text = "Enjoy";
            $message = "
                Congratulations &#128640;&#129392;, {$item['fullname']}, <br>
                You have received a payment of <b>{$item['amount']}</b> from NairaQuiz. <br>
                We ensure to reward everyone accordingly for their efforts. <br>
                Best wishes from the NairaQuiz team.<br><br>
                <a href='$link' target='_blank'><b>$text</b></a>
            ";
            send_email($subject, $item['email'], $message);

            // Insert notification
            $notification_title = 'won a quiz format';
            $user_notification_details = "Congratulations, {$item['fullname']}! <br>You have received a payment of <b>{$item['amount']}</b> from NairaQuiz.";
            $admin_notification_details = "<b>{$item['fullname']}</b> has been paid <b>{$item['amount']}</b> from NairaQuiz";
            $notification_type = 'quiz_payout';
            $notification_date = date('Y-m-d H:i:s');
            $notification_status = 'Unseen';

            $stmt = $conn->prepare("INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_receiver, notification_date, notification_status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $notification_title, $user_notification_details, $notification_type, $item['email'], $notification_date, $notification_status);
            $stmt->execute();
            
            // Send admin mail and notification
            $admin_subject = 'New Payout';
            $admin_message = "
              Hello Admin, <br>
              We are thrilled to inform you that a user has just been paid on NairaQuiz.  <br>
              Here are the details of the user: <br>
              <center>
                Fullname: <b>{$item['fullname']}</b><br>
                Amount paid: <b>{$item['amount']}</b><br>
                Reference: <b>{$item['reference']}</b><br>
                Date: <b>$notification_date</b><br>
              </center>
              <br>
              <a href='https://nairaquiz.com/admin/' target='_blank'><b>Visit Dashboard</b></a>
            ";
            
            foreach ($admin_emails as $address) {
                $insertNotif = $conn->prepare("
                    INSERT INTO general_notifications (
                        notification_title,
                        notification_details,
                        notification_type,
                        notification_receiver,
                        notification_date,
                        notification_status
                    ) VALUES (?, ?, ?, ?, ?, ?)
                ");
                $insertNotif->bind_param(
                    "ssssss",
                    $notification_title,
                    $admin_notification_details,
                    $notification_type,
                    $address,
                    $notification_date,
                    $notification_status
                );
                $insertNotif->execute();
                $insertNotif->close();
                
                send_email($admin_subject, $address, $admin_message);
            }
        }

        // Optional delay
        sleep(5);
    }
}

$beneficiaries = $_POST['beneficiaries'] ?? '';

if (empty($beneficiaries)) {
    handleErrors("No payment data received");
}

$transfer_recipients = json_decode($beneficiaries);

if (!$transfer_recipients) {
    handleErrors("Failed to decode JSON data");
}

$payoutArray = [];

foreach ($transfer_recipients as $value) {
    $payoutArray[] = [
        'fullname'  => $value->name ?? '',
        'email'     => $value->email ?? '',
        'amount'    => $value->amount ?? '',
        'reference' => $value->reference ?? '',
    ];
}

if (!empty($payoutArray)) {
    initiateTransfer($payoutArray);
    handleSuccess('Transfers queued successfully');
} else {
    handleErrors("No payment data available");
}
?>