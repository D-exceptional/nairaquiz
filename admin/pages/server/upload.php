<?php

require 'conn.php';

$response = array();

// Get incoming request data from JS
$questionsObject = $_POST['questions'];
$filename = $_POST['filename'];

if (!empty($questionsObject) && !empty($filename)) {
    // Validate JSON format
    $loopObject = json_decode($questionsObject);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $response = array('Info' => 'Invalid JSON format');
        echo json_encode($response);
        exit();
    }

    // Prepare a statement to avoid SQL injection for filename
    $stmt = $conn->prepare("SELECT file_name FROM question_files WHERE file_name = ?");
    $stmt->bind_param("s", $filename);
    if (!$stmt->execute()) {
        $response = array('Info' => 'Database error while checking filename');
        echo json_encode($response);
        exit();
    }
    $result = $stmt->get_result();

    // Check for duplicate filenames
    if ($result->num_rows > 0) {
        $response = array('Info' => 'File has been uploaded before');
        echo json_encode($response);
        exit();
    }

    // Flag to track successful insertions
    $allInserted = true;

    //Question tracker
    $tracker = 0;
    $total = count($loopObject);

    // Loop through each object in the array
    foreach ($loopObject as $value) {
        $question = $value->question;
        $option_one = $value->answers->a;
        $option_two = $value->answers->b;
        $option_three = $value->answers->c;
        //$option_four = $value->answers->d;
        $option_four = str_replace("-", "", $value->answers->d);
        $answer = $value->correctAnswer;

        // Prepare a statement to avoid SQL injection for questions
        $stmt = $conn->prepare("SELECT question_details FROM questions WHERE question_details = ?");
        $stmt->bind_param("s", $question);
        if (!$stmt->execute()) {
            $response = array('Info' => 'Database error while checking question');
            echo json_encode($response);
            exit();
        }
        $result = $stmt->get_result();

        // Check for duplicates
        if ($result->num_rows > 0) {
            continue; // Skip this question if it already exists
        }

        // Prepare insert statement
        $insertStmt = $conn->prepare("INSERT INTO questions (question_details, option_one, option_two, option_three, option_four, correct_option) VALUES (?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("ssssss", $question, $option_one, $option_two, $option_three, $option_four, $answer);

        // Execute insert statement and check for errors
        if (!$insertStmt->execute()) {
            $allInserted = false; // Set flag to false if any insert fails
        }
        else{
            $tracker++;
        }
    }

    // Only insert the filename if all questions were successfully inserted
    if ($allInserted) {
        $insertFileStmt = $conn->prepare("INSERT INTO question_files (file_name) VALUES (?)");
        $insertFileStmt->bind_param("s", $filename);
        if (!$insertFileStmt->execute()) {
            $response = array('Info' => 'Database error while inserting filename');
            echo json_encode($response);
            exit();
        }
        $response = array('Info' => 'Questions uploaded successfully', 'details' => "$tracker / $total questions uploaded successfully");
    } else {
        $response = array('Info' => 'Failed to upload some questions');
    }
} else {
    $response = array('Info' => 'Empty request data');
}

echo json_encode($response);
mysqli_close($conn);
exit();

?>
