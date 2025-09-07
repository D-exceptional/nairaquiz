<?php
// Set the time zone to Africa/Lagos
date_default_timezone_set("Africa/Lagos");

require 'conn.php';

header('Content-Type: application/json');

$data = [];

$email = $_POST['email'] ?? '';
$name = $_POST['name'] ?? '';
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';

$date = date('Y-m-d');
$time = date('H:i');

if (!empty($email) && !empty($name) && !empty($subject) && !empty($message)) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Insert mail into mailbox table using prepared statement
        $insert_sql = "INSERT INTO mailbox (mail_type, mail_subject, mail_sender, mail_date, mail_time, mail_message, mail_filename, mail_extension) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $mail_type = 'Text';
        $mail_filename = 'null';
        $mail_extension = 'null';

        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ssssssss", $mail_type, $subject, $name, $date, $time, $message, $mail_filename, $mail_extension);

        if ($stmt->execute()) {
            $data = ['Info' => 'Message sent successfully'];

            // Prepare notification insertion
            $notification_title = 'sent a mail';
            $notification_details = 'An incoming mail was received';
            $notification_type = 'incoming_mail';
            $notification_date = date('Y-m-d H:i:s');
            $notification_status = 'Unseen';

            // Get all admin emails
            $admin_query = "SELECT email FROM users WHERE user_type = 'Admin'";
            $result = $conn->query($admin_query);

            if ($result && $result->num_rows > 0) {
                $notif_sql = "INSERT INTO general_notifications 
                              (notification_title, notification_details, notification_type, notification_receiver, notification_date, notification_status)
                              VALUES (?, ?, ?, ?, ?, ?)";
                $notif_stmt = $conn->prepare($notif_sql);

                while ($row = $result->fetch_assoc()) {
                    $admin_email = $row['email'];
                    $notif_stmt->bind_param("ssssss", $notification_title, $notification_details, $notification_type, $admin_email, $notification_date, $notification_status);
                    $notif_stmt->execute();
                }
                $notif_stmt->close();
            }
        } else {
            $data = ['Info' => 'Error sending message'];
        }

        $stmt->close();
    } else {
        $data = ['Info' => 'Email is not valid'];
    }
} else {
    $data = ['Info' => 'Fill out all fields before submitting'];
}

echo json_encode($data, JSON_FORCE_OBJECT);

$conn->close();
exit();
?>
