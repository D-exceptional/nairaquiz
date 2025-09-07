<?php

// Set JSON response header
header('Content-Type: application/json');

require 'session.php';

$data = [];

// Assuming $fullname is already defined in session or earlier
if (!isset($fullname) || empty($fullname)) {
    echo json_encode(['Info' => 'Sender fullname is missing']);
    exit;
}

// Prepare and execute query
$stmt = $conn->prepare("SELECT * FROM mailbox WHERE mail_sender = ?");
$stmt->bind_param("s", $fullname);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    $data = ['Info' => 'No mail found'];
}

// Output JSON response
echo json_encode($data, JSON_FORCE_OBJECT);

$stmt->close();
$conn->close();
exit;

?>
