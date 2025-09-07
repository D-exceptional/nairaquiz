<?php

require 'conn.php';

$data = [];

$email = $_POST['email'] ?? '';

if (!empty($email)) {
    // Prepare statement to check admin email
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ? AND user_type = 'Admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $data = [
            'Info' => 'Email is available',
            'page' => ['link' => "https://nairaquiz.com/admin/recover-password.html?email=" . urlencode($email)]
        ];
    } else {
        $data = ['Info' => 'No record found'];
    }
    $stmt->close();
} else {
    $data = ['Info' => 'Email field is empty'];
}

echo json_encode($data, JSON_FORCE_OBJECT);
$conn->close();

?>
