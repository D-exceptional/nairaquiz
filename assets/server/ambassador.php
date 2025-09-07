<?php

require 'conn.php';
require 'mailer.php';

date_default_timezone_set("Africa/Lagos");
header('Content-Type: application/json');

$data = [];

$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$contact = $_POST['contact'] ?? '';
$country = $_POST['country'] ?? '';
$code = $_POST['code'] ?? '';

// Validation
if (!empty($fullname) && !empty($email) && !empty($contact) && !empty($country) && !empty($code)) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

        // Check if ambassador already exists
        $stmt = $conn->prepare("SELECT email FROM ambassadors WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = ['Info' => 'Ambassador already registered'];
        } else {
            $fullname = ucwords($fullname);
            $formattedContact = $code . substr($contact, 1);
            $status = 'Active';
            $date = date('Y-m-d H:i');

            // Insert ambassador
            $insert = $conn->prepare("INSERT INTO ambassadors (fullname, email, contact, country, created_on, ambassador_status) VALUES (?, ?, ?, ?, ?, ?)");
            $insert->bind_param("ssssss", $fullname, $email, $formattedContact, $country, $date, $status);

            if ($insert->execute()) {
                // Get ambassador ID
                $get_id = $conn->prepare("SELECT ambassadorID FROM ambassadors WHERE email = ?");
                $get_id->bind_param("s", $email);
                $get_id->execute();
                $result_id = $get_id->get_result();
                $row = $result_id->fetch_assoc();
                $ambassadorID = $row['ambassadorID'];
                $referral_link = "https://nairaquiz.com/signup?ref=$ambassadorID";

                // Create referral track
                $referral = $conn->prepare("INSERT INTO referral_track (referral_link, total_referrals, ambassadorID) VALUES (?, 0, ?)");
                $referral->bind_param("si", $referral_link, $ambassadorID);
                $referral->execute();

                // Create admin notifications
                $notification_title = 'applied on the site';
                $notification_details = "New ambassador, $fullname, applied on the site";
                $notification_type = 'ambassador_application';
                $notification_date = date('Y-m-d H:i:s');
                $notification_status = 'Unseen';

                $admins = $conn->prepare("SELECT email FROM users WHERE user_type = 'Admin'");
                $admins->execute();
                $admin_results = $admins->get_result();

                while ($admin = $admin_results->fetch_assoc()) {
                    $admin_email = $admin['email'];

                    $notif = $conn->prepare("INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_receiver, notification_date, notification_status) VALUES (?, ?, ?, ?, ?, ?)");
                    $notif->bind_param("ssssss", $notification_title, $notification_details, $notification_type, $admin_email, $notification_date, $notification_status);
                    $notif->execute();

                    // Send admin email
                    $admin_subject = "Ambassador Application";
                    $admin_message = "
                        Hello Admin,<br>
                        A new ambassador, <b>$fullname</b>, just applied on the site.<br>
                        Your further directive is needed.
                    ";
                    send_email($admin_subject, $admin_email, $admin_message);
                }

                // Send confirmation email to ambassador
                $user_subject = "Successful Application";
                $user_message = "
                    Hi <b>$fullname</b>,<br>
                    Your ambassadorship application on NairaQuiz was successful.<br>
                    Your ambassadorship link for driving traffic to the site is: <b>$referral_link</b><br>
                    Welcome to a world of opportunities and rewards.<br>
                    Further directives will be given to you shortly.
                ";
                send_email($user_subject, $email, $user_message);

                $data = ['Info' => 'Application successful'];
            } else {
                $data = ['Info' => 'Error occurred while registering you'];
            }
        }

        $stmt->close();
    } else {
        $data = ['Info' => 'Supplied email is not valid'];
    }
} else {
    $data = ['Info' => 'All fields must be filled up'];
}

echo json_encode($data, JSON_FORCE_OBJECT);
$conn->close();
exit();
