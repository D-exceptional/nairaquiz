<?php

require 'session.php';
header('Content-Type: application/json');

$data = ['Info' => 'Invalid request'];

// Validate and sanitize input
if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $workerID = intval($_POST['id']);

    // Check if worker exists
    $checkStmt = $conn->prepare("SELECT 1 FROM workers WHERE workerID = ?");
    $checkStmt->bind_param("i", $workerID);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        // Begin transaction
        $conn->begin_transaction();

        try {
            // Delete from upload_track
            $delTrackStmt = $conn->prepare("DELETE FROM upload_track WHERE workerID = ?");
            $delTrackStmt->bind_param("i", $workerID);
            $delTrackStmt->execute();
            $delTrackStmt->close();

            // Delete from workers
            $delWorkerStmt = $conn->prepare("DELETE FROM workers WHERE workerID = ?");
            $delWorkerStmt->bind_param("i", $workerID);
            $delWorkerStmt->execute();
            $delWorkerStmt->close();

            // Commit transaction
            $conn->commit();
            $data = ['Info' => 'Worker deleted successfully'];
        } catch (Exception $e) {
            $conn->rollback();
            $data = ['Info' => 'Error deleting worker'];
        }
    } else {
        $data = ['Info' => 'Worker not found'];
    }

    $checkStmt->close();
}

echo json_encode($data, JSON_FORCE_OBJECT);
$conn->close();
exit();

?>
