<?php

require 'conn.php';
require 'mailer.php';

header('Content-Type: application/json');

// Set timezone
date_default_timezone_set("Africa/Lagos");

$data = [];

$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$contact = $_POST['contact'] ?? '';
$password = $_POST['password'] ?? '';
$country = $_POST['country'] ?? '';
$code = $_POST['code'] ?? '';
$currency = $_POST['currency'] ?? '';

if (!empty($fullname) && !empty($email) && !empty($contact) && !empty($password) && !empty($country) && !empty($code) && !empty($currency)) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

        // Check if email already exists
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ? AND user_type = 'User'");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $data = ['Info' => 'User already registered'];
            $stmt->close();
        } else {
            $stmt->close();

            // Format fullname and contact
            $fullname = ucwords($fullname);
            $newContact = substr($contact, 1);
            $formattedContact = $code . $newContact;
            $status = 'Active';
            $date = date('Y-m-d H:i');
            $hashPassword = password_hash($password, PASSWORD_BCRYPT);
            $type = 'User';

            // Insert user
            $insertStmt = $conn->prepare("INSERT INTO users (user_profile, fullname, email, contact, country, user_password, created_on, user_type, user_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $nullProfile = 'null'; // or NULL if your DB allows
            $insertStmt->bind_param(
                'sssssssss',
                $nullProfile,
                $fullname,
                $email,
                $formattedContact,
                $country,
                $hashPassword,
                $date,
                $type,
                $status
            );

            if ($insertStmt->execute()) {
                $userID = $conn->insert_id;

                // Create main wallet
                $wallet_amount = 0;
                $wallet_status = "Active";
                $account_number = 0;
                $bank = "Null";
                $bank_code = "Null";
                $recipient_code = "Null";

                $walletStmt = $conn->prepare("INSERT INTO wallet (wallet_amount, wallet_currency, wallet_status, account_number, bank, bank_code, recipient_code, userID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $walletStmt->bind_param(
                    'ississsi',
                    $wallet_amount,
                    $currency,
                    $wallet_status,
                    $account_number,
                    $bank,
                    $bank_code,
                    $recipient_code,
                    $userID
                );
                $walletStmt->execute();
                $walletStmt->close();

                // Create savings wallet
                $savingsStmt = $conn->prepare("INSERT INTO wallet_savings (wallet_amount, userID) VALUES (?, ?)");
                $savingsStmt->bind_param('ii', $wallet_amount, $userID);
                $savingsStmt->execute();
                $savingsStmt->close();

                // Add notifications for admins
                $notification_title = 'registered on the site';
                $notification_details = "New user, $fullname, registered on the site";
                $notification_type = 'user_registration';
                $notification_date = date('Y-m-d H:i:s');
                $notification_status = 'Unseen';

                $adminEmailsResult = $conn->query("SELECT email FROM users WHERE user_type = 'Admin'");
                if ($adminEmailsResult) {
                    while ($row = $adminEmailsResult->fetch_assoc()) {
                        $admin_email = $row['email'];
                        $notifStmt = $conn->prepare("INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_receiver, notification_date, notification_status) VALUES (?, ?, ?, ?, ?, ?)");
                        $notifStmt->bind_param('ssssss', $notification_title, $notification_details, $notification_type, $admin_email, $notification_date, $notification_status);
                        $notifStmt->execute();
                        $notifStmt->close();
                    }
                    $adminEmailsResult->free();
                }

                // Send success email
                $subject = "Successful Registration";
                $message = "
                    Hi <b>$fullname</b>, 
                    <br>
                    Your registration on NairaQuiz was successful.
                    <br>
                    Welcome to a world of opportunities and rewards.
                    <br>
                    Login to your account via this link <a href='https://nairaquiz.com/login'>Login to dashboard</a>
                ";

                send_email($subject, $email, $message);

                $data = ['Info' => 'You have registered successfully'];
            } else {
                $data = ['Info' => 'Error occurred while registering you'];
            }
            $insertStmt->close();
        }
    } else {
        $data = ['Info' => 'Supplied email is not valid'];
    }
} else {
    $data = ['Info' => 'All fields must be filled up'];
}

echo json_encode($data, JSON_FORCE_OBJECT);
$conn->close();
exit();
?>
