<?php

require 'session.php';

header('Content-Type: application/json'); // Set JSON response header

$response = ['Info' => 'No email supplied'];

if (!empty($email)) {

    // Prepare SELECT statement
    $stmt = $conn->prepare("SELECT 1 FROM investor_notifications WHERE notification_receiver = ? AND notification_status = 'Unseen'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Prepare UPDATE statement
        $update_stmt = $conn->prepare("UPDATE investor_notifications SET notification_status = 'Seen' WHERE notification_receiver = ?");
        $update_stmt->bind_param("s", $email);

        if ($update_stmt->execute()) {
            $response['Info'] = "Status updated successfully";
        } else {
            $response['Info'] = "Error updating status";
        }

        $update_stmt->close();
    } else {
        $response['Info'] = "No notifications available";
    }

    $stmt->close();
}

echo json_encode($response, JSON_FORCE_OBJECT);
$conn->close();
exit();

?>
