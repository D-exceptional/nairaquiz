<?php

require 'conn.php';
require 'mailer.php';

date_default_timezone_set("Africa/Lagos");
header('Content-Type: application/json');

$data = [];

// Sanitize and collect inputs
$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$contact = $_POST['contact'] ?? '';
$country = $_POST['country'] ?? '';
$code = $_POST['code'] ?? '';

// Validate input
if (!empty($fullname) && !empty($email) && !empty($contact) && !empty($country) && !empty($code)) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Check if worker already exists
        $check_stmt = $conn->prepare("SELECT email FROM workers WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $data = ['Info' => 'Worker already registered'];
        } else {
            $fullname = ucwords($fullname);
            $formattedContact = $code . substr($contact, 1);
            $status = 'Active';
            $date = date('Y-m-d H:i');

            // Insert new worker
            $insert_stmt = $conn->prepare("INSERT INTO workers (fullname, email, contact, country, created_on, worker_status) VALUES (?, ?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("ssssss", $fullname, $email, $formattedContact, $country, $date, $status);

            if ($insert_stmt->execute()) {
                $data = ['Info' => 'Application successful'];

                // Get worker ID
                $id_stmt = $conn->prepare("SELECT workerID FROM workers WHERE email = ?");
                $id_stmt->bind_param("s", $email);
                $id_stmt->execute();
                $id_result = $id_stmt->get_result();
                $worker = $id_result->fetch_assoc();
                $workerID = $worker['workerID'];

                // Create upload tracker
                $track_stmt = $conn->prepare("INSERT INTO upload_track (total_points, workerID) VALUES (0, ?)");
                $track_stmt->bind_param("i", $workerID);
                $track_stmt->execute();

                // Notification details
                $notification_title = 'applied on the site';
                $notification_details = "New worker, $fullname, applied on the site";
                $notification_type = 'worker_application';
                $notification_date = date('Y-m-d H:i:s');
                $notification_status = 'Unseen';

                // Fetch admin emails
                $admin_stmt = $conn->prepare("SELECT email FROM users WHERE user_type = 'Admin'");
                $admin_stmt->execute();
                $admin_result = $admin_stmt->get_result();

                while ($admin = $admin_result->fetch_assoc()) {
                    $admin_email = $admin['email'];

                    // Insert notification
                    $notif_stmt = $conn->prepare("INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_receiver, notification_date, notification_status) VALUES (?, ?, ?, ?, ?, ?)");
                    $notif_stmt->bind_param("ssssss", $notification_title, $notification_details, $notification_type, $admin_email, $notification_date, $notification_status);
                    $notif_stmt->execute();

                    // Send email to admin
                    $admin_subject = "Worker Application";
                    $admin_message = "
                        Hello Admin,<br>
                        A new worker, <b>$fullname</b>, just applied on the site.<br>
                        Your further directive is needed.
                    ";
                    send_email($admin_subject, $admin_email, $admin_message);
                }

                // Send confirmation email to user
                $subject = "Successful Application";
                $message = "
                    Hi <b>$fullname</b>,<br>
                    Your application on NairaQuiz was successful.<br>
                    Welcome to a world of opportunities and rewards.<br>
                    Further directives will be given to you shortly.
                ";
                send_email($subject, $email, $message);
            } else {
                $data = ['Info' => 'Error occurred while registering you'];
            }
        }
        $check_stmt->close();
    } else {
        $data = ['Info' => 'Supplied email is not valid'];
    }
} else {
    $data = ['Info' => 'All fields must be filled up'];
}

// Output the response
echo json_encode($data, JSON_FORCE_OBJECT);
$conn->close();
exit();
