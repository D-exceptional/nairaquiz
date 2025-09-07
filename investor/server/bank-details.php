<?php
require 'session.php'; // Assumes $userID and $email are defined here

$response = ['Info' => 'Some fields are empty'];

$account = $_POST['account'] ?? '';
$bank = $_POST['bank'] ?? '';

if (!empty($account) && !empty($bank)) {
    // Check if wallet record exists
    $checkStmt = $conn->prepare("SELECT 1 FROM investor_finance WHERE investorID = ?");
    $checkStmt->bind_param("i", $investorID);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        // Update existing wallet record
        $updateStmt = $conn->prepare("UPDATE investor_finance SET account_number = ?, bank_name = ? WHERE investorID = ?");
        $updateStmt->bind_param("dsi", $account, $bank, $investorID);

        if ($updateStmt->execute()) {
            $response['Info'] = "Details updated successfully";
        } else {
            $response['Info'] = "Something went wrong";
        }

        $updateStmt->close();
    } else {
        // Insert new wallet record
        $insertStmt = $conn->prepare("INSERT INTO investor_finance (account_number, bank_name, investorID) VALUES (?, ?, ?)");
        $insertStmt->bind_param("dsi", $account, $bank, $investorID);

        if ($insertStmt->execute()) {
            $response['Info'] = "Details added successfully";
        } else {
            $response['Info'] = "Something went wrong";
        }

        $insertStmt->close();
    }
    $checkStmt->close();
}

echo json_encode($response, JSON_FORCE_OBJECT);
$conn->close();
exit();
?>
