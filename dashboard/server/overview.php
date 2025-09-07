<?php

require 'session.php';         // Provides $userID, $email
require 'format_number.php';   // Provides format_number($value, $decimal)
require 'conn.php';            // Ensure DB connection is included

header('Content-Type: application/json');

$response = [];

// ─────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────

function fetchSingle(mysqli $conn, string $query, string $paramType, $paramValue) {
    $stmt = $conn->prepare($query);
    if (!$stmt) return null;

    $stmt->bind_param($paramType, $paramValue);
    $stmt->execute();
    $result = $stmt->get_result();
    $value = $result->fetch_assoc();
    $stmt->close();

    return $value ?: [];
}

function fetchCount(mysqli $conn, string $query, string $paramType, $paramValue) {
    $stmt = $conn->prepare($query);
    if (!$stmt) return 0;

    $stmt->bind_param($paramType, $paramValue);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    return format_number($count ?? 0, 1);
}

function fetchAmount(mysqli $conn, string $query, string $paramType, $paramValue, $type) {
    $row = fetchSingle($conn, $query, $paramType, $paramValue);
    $amount = isset($row) ? (float) array_values($row)[0] : 0;
    return $type === 'Processed' ? number_format($amount, 2, '.', ',') : $amount;
}

// ─────────────────────────────────────────────────────────────
// Stats Collection
// ─────────────────────────────────────────────────────────────

$response = [
    'trialsCount'       => fetchCount($conn, "SELECT COUNT(DISTINCT trial_date) FROM quiz_trials WHERE userID = ?", 'i', $userID),
    'playsCount'        => fetchCount($conn, "SELECT COUNT(*) FROM session_players WHERE userID = ?", 'i', $userID),
    'winsCount'         => fetchCount($conn, "SELECT COUNT(*) FROM quiz_trials WHERE userID = ? AND trial_points = 1", 'i', $userID),
    'mailCount'         => fetchCount($conn, "SELECT COUNT(*) FROM mailbox WHERE mail_receiver = ?", 's', $email),
    'notificationCount' => fetchCount($conn, "SELECT COUNT(*) FROM general_notifications WHERE notification_receiver = ? AND notification_status = 'Unseen'", 's', $email),
    'withdrawalAmount'  => fetchAmount($conn, "SELECT wallet_amount FROM wallet_savings WHERE userID = ?", 'i', $userID, 'Processed'),
    'walletAmount'      => fetchAmount($conn, "SELECT wallet_amount FROM wallet WHERE userID = ?", 'i', $userID, 'Processed'),
    'payoutAmount'      => fetchAmount($conn, "SELECT SUM(payment_amount) FROM withdrawals WHERE userID = ? AND payment_status = 'Completed'", 'i', $userID, 'Processed'),
    'totalPayment'      => fetchAmount($conn, "SELECT SUM(fund_amount) FROM wallet_fund WHERE userID = ? AND fund_status = 'Completed'", 'i', $userID, 'Processed'),
    'pendingPayment'    => fetchAmount($conn, "SELECT SUM(fund_amount) FROM wallet_fund WHERE userID = ? AND fund_status = 'Pending'", 'i', $userID, 'Processed'),
    'fixedWalletAmount'      => fetchAmount($conn, "SELECT wallet_amount FROM wallet WHERE userID = ?", 'i', $userID, 'Raw'),
    'fixedWithdrawalAmount'  => fetchAmount($conn, "SELECT wallet_amount FROM wallet_savings WHERE userID = ?", 'i', $userID, 'Raw'),
];

// ─────────────────────────────────────────────────────────────
// Output
// ─────────────────────────────────────────────────────────────

echo json_encode($response);
$conn->close();
exit;
