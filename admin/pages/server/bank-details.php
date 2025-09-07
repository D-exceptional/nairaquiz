<?php
require 'session.php'; // Assumes $userID and $email are defined here

$response = ['Info' => 'Some fields are empty'];

$account = $_POST['account'] ?? '';
$bank = $_POST['bank'] ?? '';
$bank_code = $_POST['code'] ?? '';
$currency = $_POST['currency'] ?? '';
$recipient = $_POST['recipient'] ?? '';

if (!empty($account) && !empty($bank) && !empty($bank_code) && !empty($currency)) {
    // Check if wallet record exists
    $checkStmt = $conn->prepare("SELECT 1 FROM wallet WHERE userID = ?");
    $checkStmt->bind_param("i", $userID);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        // Update existing wallet record
        $updateStmt = $conn->prepare("UPDATE wallet SET account_number = ?, bank = ?, bank_code = ?, recipient_code = ? WHERE userID = ?");
        $updateStmt->bind_param("ssssi", $account, $bank, $bank_code, $recipient, $userID);

        if ($updateStmt->execute()) {
            $response['Info'] = "Details updated successfully";
        } else {
            $response['Info'] = "Something went wrong";
        }

        $updateStmt->close();
    } else {
        // Insert new wallet record
        $insertStmt = $conn->prepare("INSERT INTO wallet (wallet_amount, wallet_currency, wallet_status, account_number, bank, bank_code, recipient_code, userID) VALUES (0, ?, 'Active', ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("sssssi", $currency, $account, $bank, $bank_code, $recipient, $userID);

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
