<?php
require 'conn.php';

header('Content-Type: application/json');

$data = [];

$email = $_POST['email'] ?? '';

if (!empty($email)) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = ['Info' => 'Email has been added'];
        } else {
            $data = ['Info' => 'Email has not been added'];
        }

        $stmt->close();
    } else {
        $data = ['Info' => 'The supplied email is not valid'];
    }
} else {
    $data = ['Info' => 'Email field is empty'];
}

echo json_encode($data, JSON_FORCE_OBJECT);

$conn->close();
exit();

?>
