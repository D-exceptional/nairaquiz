<?php

require 'session.php';

// Set JSON response header
header('Content-Type: application/json');

$data = [];

// Validate and sanitize input
$question     = trim($_POST['details'] ?? '');
$option_one   = trim($_POST['opt1'] ?? '');
$option_two   = trim($_POST['opt2'] ?? '');
$option_three = trim($_POST['opt3'] ?? '');
$option_four  = trim($_POST['opt4'] ?? '');
$answer       = trim($_POST['answer'] ?? '');

// Check for empty fields
if (
    !empty($question) &&
    !empty($option_one) &&
    !empty($option_two) &&
    !empty($option_three) &&
    !empty($option_four) &&
    !empty($answer)
) {
    // Check for existing question
    $checkStmt = $conn->prepare("SELECT 1 FROM questions WHERE question_details = ? OR question_details LIKE ?");
    $likeQuestion = "%$question%";
    $checkStmt->bind_param("ss", $question, $likeQuestion);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $data = ['Info' => 'A question of this same or similar details already exists'];
    } else {
        // Insert the new question
        $insertStmt = $conn->prepare("
            INSERT INTO questions (
                question_details,
                option_one,
                option_two,
                option_three,
                option_four,
                correct_option
            ) VALUES (?, ?, ?, ?, ?, ?)
        ");
        $insertStmt->bind_param("ssssss", $question, $option_one, $option_two, $option_three, $option_four, $answer);

        if ($insertStmt->execute()) {
            $data = ['Info' => 'Question added successfully'];
        } else {
            $data = ['Info' => 'Error adding question'];
        }

        $insertStmt->close();
    }

    $checkStmt->close();
} else {
    $data = ['Info' => 'Some fields are empty'];
}

// Output JSON
echo json_encode($data, JSON_FORCE_OBJECT);
$conn->close();
exit();
