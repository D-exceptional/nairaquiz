<?php

require 'session.php'; // Assumes $userID and $email are defined here

$data = ['Info' => 'Some fields are empty'];

$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';

if (!empty($currentPassword) && !empty($newPassword)) {
    // Prepare statement to fetch password
    $stmt = $conn->prepare("SELECT investor_password FROM investors WHERE investorID = ?");
    $stmt->bind_param("i", $investorID);
    $stmt->execute();
    $stmt->bind_result($dbPassword);
    if ($stmt->fetch()) {
        // Verify current password
        if (password_verify($currentPassword, $dbPassword)) {
            $stmt->close();

            // Hash new password
            $hashPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Update password
            $updateStmt = $conn->prepare("UPDATE investors SET investor_password = ? WHERE investorID = ?");
            $updateStmt->bind_param("si", $hashPassword, $investorID);

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
