<?php

require 'session.php';
header('Content-Type: application/json');

$response = [];

$reference = $_POST['reference'] ?? '';
$payment = $_POST['payment'] ?? '';

if (!empty($reference) && !empty($payment)) {
    // Define tables and columns
    $payment_table     = ($payment === 'Game') ? 'wallet_fund' : 'investor_plans';
    $payment_column    = ($payment === 'Game') ? 'fund_txref' : 'plan_reference';
    $receipt_table     = ($payment === 'Game') ? 'wallet_fund_receipt' : 'investor_receipts';
    $receipt_column    = ($payment === 'Game') ? 'receipt_name' : 'receipt_reference';
    $receipt_file_col  = ($payment === 'Game') ? 'receipt_image' : 'receipt_filename';

    // Step 1: Check if payment reference exists
    $stmt = $conn->prepare("SELECT 1 FROM $payment_table WHERE $payment_column = ?");
    $stmt->bind_param("s", $reference);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();

        // Step 2: Fetch the receipt file name
        $stmt = $conn->prepare("SELECT $receipt_file_col FROM $receipt_table WHERE $receipt_column = ?");
        $stmt->bind_param("s", $reference);
        $stmt->execute();
        $stmt->bind_result($receiptFile);
        $stmt->fetch();
        $stmt->close();

        if (!empty($receiptFile)) {
            $receiptPath = "../../../documents/" . $receiptFile;

            // Step 3: Try to delete the actual receipt file
            if (file_exists($receiptPath)) {
                if (!unlink($receiptPath)) {
                    error_log("Failed to delete receipt file: $receiptPath");
                    // Optional: you could return an error here
                }
            }
        }

        // Step 4: Delete receipt record
        $stmt = $conn->prepare("DELETE FROM $receipt_table WHERE $receipt_column = ?");
        $stmt->bind_param("s", $reference);
        if ($stmt->execute()) {
            $stmt->close();

            // Step 5: Delete payment record
            $stmt = $conn->prepare("DELETE FROM $payment_table WHERE $payment_column = ?");
            $stmt->bind_param("s", $reference);
            if ($stmt->execute()) {
                $response = ['Info' => 'Payment deleted successfully'];
            } else {
                $response = ['Info' => 'Error deleting payment record'];
                error_log("Delete payment record failed: " . $stmt->error);
            }
        } else {
            $response = ['Info' => 'Error deleting payment receipt record'];
            error_log("Delete receipt failed: " . $stmt->error);
        }
        $stmt->close();
    } else {
        $response = ['Info' => 'Payment details not found'];
        $stmt->close();
    }
} else {
    $response = ['Info' => 'Payment reference missing'];
}

echo json_encode($response);
$conn->close();
exit();
?>
