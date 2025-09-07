<?php

require 'session.php';

// Set JSON response header
header('Content-Type: application/json');

$data = [];

// Sanitize and validate input
$question = trim($_POST['question'] ?? '');

if (!empty($question)) {
    // Prepare the SQL statement to check if a similar question exists
    $stmt = $conn->prepare("SELECT 1 FROM questions WHERE question_details = ? OR question_details LIKE ?");
    $likeQuestion = "%$question%";
    $stmt->bind_param("ss", $question, $likeQuestion);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $data = ['Info' => 'A question of this same or similar details already exists'];
    } else {
        $data = ['Info' => 'You are good to go'];
    }

    $stmt->close();
} else {
    $data = ['Info' => 'Question field is empty'];
}

// Return JSON response
echo json_encode($data, JSON_FORCE_OBJECT);
$conn->close();
exit();

?>
