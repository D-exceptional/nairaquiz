<?php

require 'conn.php';

header('Content-Type: application/json');

$data = [];

$question = $_POST['question'] ?? '';

if (!empty($question)) {
    // Prepare SQL to check for exact or similar questions using LIKE
    $sql = "SELECT question_details FROM questions WHERE question_details = ? OR question_details LIKE ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $data = ['Info' => 'Server error: failed to prepare statement'];
        echo json_encode($data, JSON_FORCE_OBJECT);
        exit();
    }
    
    $likeQuestion = "%{$question}%";
    $stmt->bind_param("ss", $question, $likeQuestion);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $data = ['Info' => 'A question of this same or similar details already exists'];
    } else {
        $data = ['Info' => 'You are good to go'];
    }

    $stmt->close();
} else {
    $data = ['Info' => 'Question field is empty'];
}

echo json_encode($data, JSON_FORCE_OBJECT);
$conn->close();
exit();

?>
