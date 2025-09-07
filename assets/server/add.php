<?php
require 'conn.php';

header('Content-Type: application/json');

$data = [];

$workerID = $_POST['id'] ?? '';
$question = $_POST['question'] ?? '';
$option_one = $_POST['opt1'] ?? '';
$option_two = $_POST['opt2'] ?? '';
$option_three = $_POST['opt3'] ?? '';
$option_four = $_POST['opt4'] ?? '';
$answer = $_POST['answer'] ?? '';

// Validate required fields
if (
    !empty($workerID) && !empty($question) && !empty($option_one) &&
    !empty($option_two) && !empty($option_three) &&
    !empty($option_four) && !empty($answer)
) {
    // Check if worker exists
    $checkWorker = $conn->prepare("SELECT total_points FROM upload_track WHERE workerID = ?");
    $checkWorker->bind_param("i", $workerID);
    $checkWorker->execute();
    $resultWorker = $checkWorker->get_result();

    if ($resultWorker->num_rows > 0) {
        $row = $resultWorker->fetch_assoc();
        $current_point = (int)$row['total_points'];
        $checkWorker->close();

        // Check for duplicate question
        $checkQuestion = $conn->prepare("SELECT question_details FROM questions WHERE question_details = ? OR question_details LIKE ?");
        $like_question = "%$question%";
        $checkQuestion->bind_param("ss", $question, $like_question);
        $checkQuestion->execute();
        $resultQuestion = $checkQuestion->get_result();

        if ($resultQuestion->num_rows > 0) {
            $data = ['Info' => 'A question with the same or similar details already exists'];
        } else {
            $checkQuestion->close();

            // Insert new question
            $insert = $conn->prepare("
                INSERT INTO questions (question_details, option_one, option_two, option_three, option_four, correct_option)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $insert->bind_param("ssssss", $question, $option_one, $option_two, $option_three, $option_four, $answer);

            if ($insert->execute()) {
                // Update points
                $new_point = $current_point + 1;
                $update = $conn->prepare("UPDATE upload_track SET total_points = ? WHERE workerID = ?");
                $update->bind_param("ii", $new_point, $workerID);
                $update->execute();
                $update->close();

                $data = ['Info' => 'Question added successfully'];
            } else {
                $data = ['Info' => 'Error adding question'];
            }

            $insert->close();
        }

        $checkQuestion->close();
    } else {
        $data = ['Info' => 'Worker details not found'];
    }

    $checkWorker->close();
} else {
    $data = ['Info' => 'Some fields are empty'];
}

// Output JSON response
echo json_encode($data, JSON_FORCE_OBJECT);
$conn->close();
exit();
