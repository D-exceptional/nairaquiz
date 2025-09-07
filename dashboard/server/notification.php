<?php

require 'session.php'; // Assumes $userID and $email are defined

header('Content-Type: application/json');

$data = [];

if (!empty($email)) {
    // Prepare SELECT statement to check for unseen notifications
    $stmt = $conn->prepare("SELECT 1 FROM general_notifications WHERE notification_receiver = ? AND notification_status = 'Unseen' LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();

        // Prepare UPDATE statement to mark notifications as seen
        $update_stmt = $conn->prepare("UPDATE general_notifications SET notification_status = 'Seen' WHERE notification_receiver = ? AND notification_status = 'Unseen'");
        $update_stmt->bind_param("s", $email);
        
        if ($update_stmt->execute()) {
            $data = ['Info' => 'Status updated successfully'];
        } else {
            $data = ['Info' => 'Error updating status'];
        }

        $update_stmt->close();
    } else {
        $data = ['Info' => 'No notifications available'];
        $stmt->close();
    }
} else {
    $data = ['Info' => 'No email supplied'];
}

// Output response
echo json_encode($data, JSON_FORCE_OBJECT);

$conn->close();
exit();
?>
