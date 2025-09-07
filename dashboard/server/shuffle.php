<?php
// Database connection parameters
require 'conn.php';

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 1; // Ensure it's an integer
$calc_limit = $limit * $limit;

$questions = [];
$response = [];

// Prepare SQL query with a placeholder for LIMIT
$query = "SELECT question_details, option_one, option_two, option_three, option_four, correct_option FROM questions ORDER BY RAND() LIMIT ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind limit as an integer (i)
$stmt->bind_param("i", $calc_limit);

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questions[] = (object) array(
            'question' => $row['question_details'],
            'answers' => array(
                'a' => $row['option_one'],
                'b' => $row['option_two'],
                'c' => $row['option_three'],
                'd' => $row['option_four'],
            ),
            'correctAnswer' => strtolower($row['correct_option']),
        );
    }

    shuffle($questions); // Shuffle the questions
    $response[] = array('questions' => $questions);
} else {
    $response[] = array('questions' => []);
}

// Send the shuffled questions as JSON
header('Content-Type: application/json');
echo json_encode($response, JSON_FORCE_OBJECT);

$stmt->close();
$conn->close();
exit();
?>
