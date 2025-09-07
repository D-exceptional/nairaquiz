<?php
require 'session.php'; // Assumes $userID is available
require 'conn.php';    // Ensure $conn is available

header('Content-Type: application/json');

function getWalletAmount(mysqli $conn, int $userID): array {
    $stmt = $conn->prepare("SELECT wallet_amount FROM wallet WHERE userID = ?");
    $stmt->bind_param("i", $userID);  // Correct type: "i" for int
    $stmt->execute();
    $stmt->bind_result($amount);

    if ($stmt->fetch()) {
        $stmt->close();
        return [
            'status' => 200,
            'message' => 'Amount fetched successfully',
            'data' => [ 'amount' => $amount ]
        ];
    }

    $stmt->close();
    return [
        'status' => 404,
        'message' => 'Wallet not found',
        'data' => [ 'amount' => 0 ]
    ];
}

if (isset($userID) && is_numeric($userID)) {
    echo json_encode(getWalletAmount($conn, (int)$userID));
} else {
    echo json_encode([
        'status' => 400,
        'message' => 'Invalid user session',
        'data' => [ 'amount' => 0 ]
    ]);
}

