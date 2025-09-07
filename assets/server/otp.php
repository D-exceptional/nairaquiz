<?php

session_start();

require 'conn.php';
require 'mailer.php';

header('Content-Type: application/json');

// Set the time zone to Africa/Lagos
date_default_timezone_set("Africa/Lagos");

$data = [];

// Function to generate a random 6-digit code
function generateRandomCode() {
    return rand(100000, 999999);
}

$verificationCode = generateRandomCode();

$email = $_POST['email'] ?? '';

if (!empty($email)) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Use prepared statement to check email existence
        $stmt = $conn->prepare("SELECT fullname FROM users WHERE email = ? AND user_type = 'User'");
        if (!$stmt) {
            $data = ['Info' => 'Server error: failed to prepare statement'];
            echo json_encode($data, JSON_FORCE_OBJECT);
            exit();
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $fullname = $row['fullname'];

            // Send email
            $subject = "Password Reset";
            $message = "
                Hi <b>$fullname</b>, 
                <br>
                Your password reset OTP is: <b>$verificationCode</b> and it expires in the next 5 minutes.
                <br>
                Enter it in the OTP input field on your password reset page.
            ";
            
            send_email($subject, $email, $message);
            
            // Store in session
            $_SESSION['otp'] = $verificationCode;      // Set session OTP
            $_SESSION['time'] = $_SERVER['REQUEST_TIME']; // Set session time
            $data = ['Info' => 'OTP Sent'];

        } else {
            $data = ['Info' => 'Email not found'];
        }

        $stmt->close();
    } else {
        $data = ['Info' => 'Email is not valid'];
    }
} else {
    $data = ['Info' => 'Email field is empty'];
}

echo json_encode($data, JSON_FORCE_OBJECT);
$conn->close();
exit();

?>
