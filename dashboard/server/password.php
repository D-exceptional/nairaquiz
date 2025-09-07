<?php

require 'session.php'; // Assumes $userID and $email are defined here

$data = ['Info' => 'Some fields are empty'];

$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';

if (!empty($currentPassword) && !empty($newPassword)) {
    // Prepare statement to fetch password
    $stmt = $conn->prepare("SELECT user_password FROM users WHERE userID = ? AND user_type = 'User'");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($dbPassword);
    if ($stmt->fetch()) {
        // Verify current password
        if (password_verify($currentPassword, $dbPassword)) {
            $stmt->close();

            // Hash new password
            $hashPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Update password
            $updateStmt = $conn->prepare("UPDATE users SET user_password = ? WHERE userID = ? AND user_type = 'User'");
            $updateStmt->bind_param("si", $hashPassword, $userID);

            if ($updateStmt->execute()) {
                $data['Info'] = "Password updated successfully";
            } else {
                $data['Info'] = "Something went wrong";
            }
            $updateStmt->close();
        } else {
            $data['Info'] = "Current password does not match";
            $stmt->close();
        }
    } else {
        $data['Info'] = "User not found";
        $stmt->close();
    }
}

echo json_encode($data, JSON_FORCE_OBJECT);
$conn->close();
exit();

?>
