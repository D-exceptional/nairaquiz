<?php 

require 'conn.php';

$data = [];

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!empty($email) && !empty($password)) {
    // Prepare to check if admin email exists
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ? AND user_type = 'Admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Email exists, proceed to update password
        //$stmt->close();

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $updateStmt = $conn->prepare("UPDATE users SET user_password = ? WHERE email = ? AND user_type = 'Admin'");
        $updateStmt->bind_param("ss", $hashedPassword, $email);

        if ($updateStmt->execute()) {
            $data = [
                'Info' => 'Password changed successfully',
                'page' => ['link' => "https://nairaquiz.com/admin/"]
            ];
        } else {
            $data = ['Info' => 'Error changing password'];
        }
        $updateStmt->close();
    } else {
        $data = ['Info' => 'No record found'];
    }
    $stmt->close();
} else {
    $data = ['Info' => 'All inputs must be filled out'];
}

echo json_encode($data, JSON_FORCE_OBJECT);
$conn->close();
exit();

?>
