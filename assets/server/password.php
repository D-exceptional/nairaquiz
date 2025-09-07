<?php 

session_start();

// Retrieve session details safely
$session_time = $_SESSION['time'] ?? null;
$session_otp = $_SESSION['otp'] ?? null;

require 'conn.php';

$data = [];

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$otp = $_POST['otp'] ?? '';
$timestamp = $_SERVER['REQUEST_TIME'] ?? time();

if (!empty($email) && !empty($password) && !empty($otp)) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if (!$session_time || !$session_otp) {
            $data = ['Info' => 'OTP session expired or not found. Please request a new OTP.'];
        } else if (($timestamp - $session_time) > 300) { // 5 minutes expiry
            $data = ['Info' => 'OTP has expired. Get a new one'];
        } else if (intval($otp) !== intval($session_otp)) {
            $data = ['Info' => 'Incorrect OTP code'];
        } else {
            // OTP valid; clear session OTP data
            unset($_SESSION['otp'], $_SESSION['time']);

            // Hash the new password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Use prepared statement to check email and update password
            $stmt = $conn->prepare("SELECT email FROM users WHERE email = ? AND user_type = 'User'");
            if (!$stmt) {
                $data = ['Info' => 'Server error: failed to prepare statement'];
            } else {
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {
                    $stmt->close();

                    $update_stmt = $conn->prepare("UPDATE users SET user_password = ? WHERE email = ? AND user_type = 'User'");
                    if (!$update_stmt) {
                        $data = ['Info' => 'Server error: failed to prepare update statement'];
                    } else {
                        $update_stmt->bind_param('ss', $hashedPassword, $email);
                        if ($update_stmt->execute()) {
                            $data = ['Info' => 'Password changed successfully'];
                        } else {
                            $data = ['Info' => 'Error changing password'];
                        }
                        $update_stmt->close();
                    }
                } else {
                    $data = ['Info' => 'No record found'];
                }
            }
        }
    } else {
        $data = ['Info' => 'Supplied email is invalid'];
    }
} else {
    $data = ['Info' => 'All inputs must be filled out'];
}

echo json_encode($data, JSON_FORCE_OBJECT);
$conn->close();
exit();

?>
