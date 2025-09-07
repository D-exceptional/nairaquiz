<?php

require 'session.php';
require 'format_number.php';

// Set response type to JSON
header('Content-Type: application/json');

// Initialize data array
$data = [];

// Use prepared statements for COUNT queries
function getCount($conn, $table) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM $table");
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $table === 'investors' ? format_number(($count - 1), 1) : format_number(($count), 1);
}

// Count rows for various tables
$userCount = getCount($conn, 'users');
$workerCount = getCount($conn, 'workers');
$ambassadorCount = getCount($conn, 'ambassadors');
$investorCount = getCount($conn, 'investors');
$questionsCount = getCount($conn, 'questions');
$trialsCount = getCount($conn, 'quiz_trials');
$notificationsCount = getCount($conn, 'general_notifications');
$mailCount = getCount($conn, 'mailbox');
$gameCount = getCount($conn, 'session_game');
$playsCount = getCount($conn, 'session_players');

// Get total game pay-ins
$totalGamePayIns = 0;
$stmt = $conn->prepare("SELECT SUM(fund_amount) FROM wallet_fund WHERE fund_status = 'Completed'");
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$totalGamePayIns = number_format($total ?? 0, 2, '.', ',');
$stmt->close();

// Get total game pay-outs
$totalGamePayOuts = 0;
$stmt = $conn->prepare("SELECT SUM(payment_amount) FROM withdrawals WHERE payment_status = 'Completed'");
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$totalGamePayOuts = number_format($total ?? 0, 2, '.', ',');
$stmt->close();

// Get pending game pay-ins
$pendingGamePayIns = 0;
$stmt = $conn->prepare("SELECT COUNT(*) FROM wallet_fund WHERE fund_status = 'Pending'");
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$pendingGamePayIns = format_number(($total), 1);
$stmt->close();

// Get pending game pay-outs
$pendingGamePayOuts = 0;
$stmt = $conn->prepare("SELECT COUNT(*) FROM withdrawals WHERE payment_status = 'Pending'");
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$pendingGamePayOuts = format_number(($total), 1);
$stmt->close();

// Get total investment pay-ins
$totalInvestmentPayIns = 0;
$stmt = $conn->prepare("SELECT SUM(plan_amount) FROM investor_plans WHERE plan_status = 'Completed'");
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$totalInvestmentPayIns = number_format($total ?? 0, 2, '.', ',');
$stmt->close();

// Get total investment pay-outs
$totalInvestmentPayOuts = 0;
$stmt = $conn->prepare("SELECT SUM(withdrawal_amount) FROM investment_withdrawal WHERE withdrawal_status = 'Completed'");
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$totalInvestmentPayOuts = number_format($total ?? 0, 2, '.', ',');
$stmt->close();

// Get pending investment pay-ins
$pendingInvestmentPayIns = 0;
$stmt = $conn->prepare("SELECT COUNT(*) FROM investor_plans WHERE plan_status = 'Pending'");
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$pendingInvestmentPayIns = format_number(($total), 1);
$stmt->close();

// Get pending investment pay-outs
$pendingInvestmentPayOuts = 0;
$stmt = $conn->prepare("SELECT COUNT(*) FROM investment_withdrawal WHERE withdrawal_status = 'Pending'");
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$pendingInvestmentPayOuts = format_number(($total), 1);
$stmt->close();

// Demo wallet (static 0.00)
$walletBalance = 0;
$stmt = $conn->prepare("SELECT wallet_amount FROM wallet WHERE userID = ?");
$stmt->bind_param('i', $userID);
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$walletBalance = number_format($total ?? 0, 2, '.', ',');
$stmt->close();

// Build final response array
$data[] = [
    'userCount' => $userCount,
    'workerCount' => $workerCount,
    'ambassadorCount' => $ambassadorCount,
    'investorCount' => $investorCount,
    'questionCount' => $questionsCount,
    'trialCount' => $trialsCount,
    'notificationsCount' => $notificationsCount,
    'mailCount' => $mailCount,
    'totalGamePayIns' => $totalGamePayIns,
    'totalGamePayOuts' => $totalGamePayOuts,
    'pendingGamePayIns' => $pendingGamePayIns,
    'pendingGamePayOuts' => $pendingGamePayOuts,
    'totalInvestmentPayIns' => $totalInvestmentPayIns,
    'totalInvestmentPayOuts' => $totalInvestmentPayOuts,
    'pendingInvestmentPayIns' => $pendingInvestmentPayIns,
    'pendingInvestmentPayOuts' => $pendingInvestmentPayOuts,
    'gameCount' => $gameCount,
    'playsCount' => $playsCount,
    'walletBalance' => $walletBalance
];

// Output JSON
echo json_encode($data, JSON_FORCE_OBJECT);

$conn->close();
exit();

?>
