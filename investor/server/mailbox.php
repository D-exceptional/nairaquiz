<?php

require 'session.php';

// Set JSON response header
header('Content-Type: application/json');

$data = [];

// Prepare statement
$stmt = $conn->prepare("SELECT * FROM investor_mailbox WHERE mail_receiver = ? ORDER BY mailID DESC");
if ($stmt) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    } else {
        $data = ['Info' => 'No mail found'];
    }

    $stmt->close();
} else {
    $data = ['Info' => 'Database error: Failed to prepare query'];
}

// Encode and return JSON
echo json_encode($data, JSON_FORCE_OBJECT);
$conn->close();
exit();

?>
