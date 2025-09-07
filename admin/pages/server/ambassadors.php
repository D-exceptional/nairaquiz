<?php
require 'session.php';

// Set response header for JSON
header('Content-Type: application/json');

$data = [];

if (isset($_POST['id']) && !empty($_POST['id'])) {
    $ambassadorID = $_POST['id'];

    // Prepare SELECT statement to check if ambassador exists
    $stmt = $conn->prepare("SELECT 1 FROM ambassadors WHERE ambassadorID = ?");
    $stmt->bind_param("i", $ambassadorID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();

        // Prepare DELETE statement for referral_track
        $delReferralStmt = $conn->prepare("DELETE FROM referral_track WHERE ambassadorID = ?");
        $delReferralStmt->bind_param("i", $ambassadorID);

        if ($delReferralStmt->execute()) {
            $delReferralStmt->close();

            // Prepare DELETE statement for ambassadors
            $delAmbassadorStmt = $conn->prepare("DELETE FROM ambassadors WHERE ambassadorID = ?");
            $delAmbassadorStmt->bind_param("i", $ambassadorID);

            if ($delAmbassadorStmt->execute()) {
                $data['Info'] = 'Ambassador deleted successfully';
            } else {
                $data['Info'] = 'Error deleting ambassador';
            }

            $delAmbassadorStmt->close();
        } else {
            $data['Info'] = 'Error deleting referral records';
        }
    } else {
        $data['Info'] = 'Ambassador not found';
        $stmt->close();
    }
} else {
    $data['Info'] = 'Invalid ambassador ID supplied';
}

echo json_encode($data, JSON_FORCE_OBJECT);
$conn->close();
exit();
?>
