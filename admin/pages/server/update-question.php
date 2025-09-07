<?php

// Set JSON response header
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "session.php";

// Initialize variables
$column = "";
$questionID = $_POST['id'];
$type = $_POST['type'];
$value = $_POST['value'];  

// Process
if (!empty($questionID) && !empty($type) && !empty($value)) {
    // Determine the column name based on the type
    if ($type === "question") {
        $column = "question_details";
    } else {
        // Check if the type is valid to prevent SQL injection
        $validColumns = ['option_one', 'option_two', 'option_three', 'option_four']; // Add any other valid columns
        if (in_array($type, $validColumns)) {
            $column = $type;
        } else {
            // Invalid type, return error
            echo json_encode(['Info' => 'Invalid type']);
            exit();
        }
    }

    // Prepare the update statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE questions SET $column = ? WHERE questionID = ?");
    $stmt->bind_param('si', $value, $questionID); // Assuming questionID is an integer

    if ($stmt->execute()) {
        // Prepare response
        $data = ['Info' => 'Detail updated'];
    } else {
        // Prepare response
        $data = ['Info' => 'Error updating question: ' . $stmt->error];
    }

    $stmt->close();
} else {
    // Prepare response
    $data = ['Info' => 'Some fields are empty'];
}

echo json_encode($data, JSON_FORCE_OBJECT);
mysqli_close($conn);
exit();
?>
