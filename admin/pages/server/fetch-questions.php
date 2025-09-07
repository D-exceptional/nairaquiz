<?php

require 'session.php';

// Set JSON response header
header('Content-Type: application/json');

// Enable error reporting (for development)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$data = [];
$questions = [];

$validCounts = ['25', '50', '100', '250', '500', '1000', 'All'];

$count = $_GET['count'] ?? '25';

if (in_array($count, $validCounts, true)) {
    if ($count === 'All') {
        $query = "SELECT questionID, question_details, option_one, option_two, option_three, option_four FROM questions ORDER BY questionID DESC";
        $stmt = $conn->prepare($query);
    } else {
        $query = "SELECT questionID, question_details, option_one, option_two, option_three, option_four FROM questions ORDER BY questionID DESC LIMIT ?";
        $stmt = $conn->prepare($query);
        $limit = (int)$count;
        $stmt->bind_param("i", $limit);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $questions[] = [
                'id' => $row['questionID'],
                'question' => $row['question_details'],
                'opt1' => $row['option_one'],
                'opt2' => $row['option_two'],
                'opt3' => $row['option_three'],
                'opt4' => $row['option_four'],
            ];
        }
        $data = ['Info' => 'Questions fetched', 'questions' => $questions];
    } else {
        $data = ['Info' => 'No questions found', 'questions' => []];
    }

    $stmt->close();
} else {
    $data = ['Info' => 'Invalid or missing count parameter'];
}

echo json_encode($data, JSON_FORCE_OBJECT);
$conn->close();
exit();
