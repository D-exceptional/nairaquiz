<?php

require 'session.php';
require 'format_number.php';

// Set response type to JSON
header('Content-Type: application/json');

// Initialize data array
$data = [];

// Use prepared statements for COUNT queries
function getCount($conn, $table, $column, $type, $value) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM $table WHERE $column = ?");
    $stmt->bind_param($type, $value);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

function getWalletAmount($table, $id, $type, $conn) {
    $stmt = $conn->prepare("SELECT wallet_amount FROM $table WHERE investorID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $type === 'Processed' ? '₦' . number_format($row['wallet_amount'], 2, '.', ',') : $row['wallet_amount'];
}

// Count rows for various tables
$mailCount = getCount($conn, 'investor_mailbox', 'mail_receiver', 's', $email);
$notificationCount = getCount($conn, 'investor_notifications', 'notification_receiver', 's', $email);
$userCount = getCount($conn, 'investor_referrals', 'facilitatorID', 'i', $investorID);
$investmentCount = getCount($conn, 'investor_plans', 'investorID', 'i', $investorID);
$withdrawalCount = getCount($conn, 'investment_withdrawal', 'investorID', 'i', $investorID);

// Get wallet amounts
$walletTierOne = getWalletAmount('wallet_tier_one', $investorID, 'Processed', $conn);
$walletTierTwo = getWalletAmount('wallet_tier_two', $investorID, 'Processed', $conn);
$walletTierThree = getWalletAmount('wallet_tier_three', $investorID, 'Processed', $conn);

// Get total investments
$totalInvestments = 0;
$status = 'Approved';
$stmt = $conn->prepare("SELECT SUM(plan_amount) FROM investor_plans WHERE plan_status = ? AND investorID = ?");
$stmt->bind_param("si", $status, $investorID);
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$totalInvestments = '₦' . number_format($total ?? 0, 2, '.', ',');
$stmt->close();

// Get total payouts
$totalPayOuts = 0;
$status = 'Completed';
$stmt = $conn->prepare("SELECT SUM(withdrawal_amount) FROM investment_withdrawal WHERE withdrawal_status = ? AND investorID = ?");
$stmt->bind_param("si", $status, $investorID);
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$totalPayOuts = '₦' . number_format($total ?? 0, 2, '.', ',');
$stmt->close();

// Build final response array
$data[] = [
    'totalInvestments' => $totalInvestments,
    'totalPayOuts' => $totalPayOuts,
    'walletOne' => $walletTierOne,
    'walletTwo' => $walletTierTwo,
    'walletThree' => $walletTierThree,
    'userCount' => format_number($userCount, 1),
    'mailCount' => format_number($mailCount, 1),
    'notificationCount' => format_number($notificationCount, 1),
    'investmentCount' => format_number($investmentCount, 1),
    'withdrawalCount' => format_number($withdrawalCount, 1),
];

// Output JSON
echo json_encode($data, JSON_FORCE_OBJECT);

$conn->close();
exit();

?>
