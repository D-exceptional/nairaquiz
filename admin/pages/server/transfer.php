<?php

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get dependencies
require 'mailer.php';
require 'conn.php';

// Output data structure
$response = [];

// Global timestamp
$date = date('Y-m-d H:i:s');

// Input sanitization
$fullname  = trim($_POST['name'] ?? '');
$email     = trim($_POST['email'] ?? '');
$amount    = trim($_POST['amount'] ?? '');
$reference = trim($_POST['reference'] ?? '');
$status    = 'Completed'; // change to 'Pending' when gateway is implemented

// Check for required fields
if ($fullname && $email && $amount && $reference) {

    // Update withdrawal status to Completed
    $stmt = $conn->prepare("UPDATE withdrawals SET payment_status = ? WHERE payment_txref = ?");
    $stmt->bind_param("ss", $status, $reference);
    $stmt->execute();

    // Email notification for user (remove when using webhook)
    $subject = "NairaQuiz Payout";
    $link = "#";
    $text = "Enjoy";
    $message = "
        Congratulations &#128640;&#129392;, $fullname, <br>
        You have received a payment of <b>$amount</b> from NairaQuiz. <br>
        We ensure to reward everyone accordingly for their efforts. <br>
        Best wishes from the NairaQuiz team.<br><br>
        <a href='$link' target='_blank'><b>$text</b></a>
    ";
    send_email($subject, $email, $message);

    // Insert user notification
    $notification_title = 'won a quiz format';
    $user_notification_details = "Congratulations, $fullname! <br>You have received a payment of <b>$amount</b> from NairaQuiz";
    $admin_notification_details = "<b>$fullname</b> has been paid <b>$amount</b> from NairaQuiz";
    $notification_type = 'quiz_payout';
    $notification_status = 'Unseen';

    $stmt = $conn->prepare("INSERT INTO general_notifications 
        (notification_title, notification_details, notification_type, notification_receiver, notification_date, notification_status) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $notification_title, $user_notification_details, $notification_type, $email, $date, $notification_status);
    $stmt->execute();
    
    // Email notification for admins (remove when using webhook)
    $admin_emails = ['chukwuebukaokeke09@gmail.com', 'krankstephen20@gmail.com'];
    
    $admin_subject = 'New Payout';
    $admin_message = "
      Hello Admin, <br>
      We are thrilled to inform you that a user has just been paid on NairaQuiz.  <br>
      Here are the details of the user: <br>
      <center>
        Fullname: <b>$fullname</b><br>
        Amount paid: <b>$amount</b><br>
        Reference: <b>$reference</b><br>
        Date: <b>$date</b><br>
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
            $date,
            $notification_status
        );
        $insertNotif->execute();
        $insertNotif->close();
        
        send_email($admin_subject, $address, $admin_message);
    }

    $response = ['Info' => 'Transfer was successful'];
} else {
    $response = ['Info' => 'Transfer details not set or invalid'];
}

// Output JSON
header('Content-Type: application/json');
echo json_encode($response, JSON_FORCE_OBJECT);

// Close connection
$conn->close();
exit();
?>