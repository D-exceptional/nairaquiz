<?php
require 'session.php';

// Set JSON response header
header('Content-Type: application/json');

$data = [];

if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $questionID = (int)$_POST['id'];

    // Check if the question exists
    $checkStmt = $conn->prepare("SELECT 1 FROM questions WHERE questionID = ?");
    $checkStmt->bind_param("i", $questionID);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $checkStmt->close();

        // Delete the question
        $deleteStmt = $conn->prepare("DELETE FROM questions WHERE questionID = ?");
        $deleteStmt->bind_param("i", $questionID);

        if ($deleteStmt->execute()) {
            $data['Info'] = 'Question deleted successfully';
        } else {
            $data['Info'] = 'Error deleting question';
        }

        $deleteStmt->close();
    } else {
        $data['Info'] = 'Question not found';
        $checkStmt->close();
    }
} else {
    $data['Info'] = 'Invalid question ID';
}

echo json_encode($data, JSON_FORCE_OBJECT);
$conn->close();
exit();
?>
