<?php
require 'conn.php';
require 'functions.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ─────────────────────────────────────────
// CONFIGURATION CONSTANTS
// ─────────────────────────────────────────
define('TOP_LEVEL_ADMINS', json_encode([1]));
define('REFERRAL_PERCENT', 0.08);
define('DOWNLINE_PERCENT', 0.03);

// ─────────────────────────────────────────
// INPUT VALIDATION
// ─────────────────────────────────────────
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$plan = $_POST['plan'] ?? '';
$amount = $_POST['amount'] ?? '';
$reference = $_POST['reference'] ?? '';

if (!$name || !$email || !$plan || !$amount || !$reference) {
    echo json_encode(['Info' => 'Some fields are empty']);
    exit();
}

// ─────────────────────────────────────────
// MAIN TRANSACTIONAL LOGIC
// ─────────────────────────────────────────
try {
    $conn->begin_transaction();

    // 1. Verify payment record
    $stmt = $conn->prepare("SELECT * FROM investor_plans WHERE plan_reference = ? AND plan_status = 'Pending'");
    $stmt->bind_param("s", $reference);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['Info' => 'Payment record not found or already processed']);
        exit();
    }

    // 2. Update payment status
    $stmt = $conn->prepare("UPDATE investor_plans SET plan_status = 'Completed' WHERE plan_reference = ?");
    $stmt->bind_param("s", $reference);
    $stmt->execute();

    // 3. Update receipt status
    $stmt = $conn->prepare("UPDATE investor_receipts SET receipt_status = 'Completed' WHERE receipt_reference = ?");
    $stmt->bind_param("s", $reference);
    $stmt->execute();

    // 4. Fetch investor account
    $stmt = $conn->prepare("SELECT investorID FROM investors WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $investor = $stmt->get_result()->fetch_assoc();

    if (!$investor) {
        $conn->rollback();
        echo json_encode(['Info' => 'Account not found']);
        exit();
    }

    $investorID = $investor['investorID'];

    // 5. Activate account only if it's their first payment
    $stmt = $conn->prepare("SELECT COUNT(*) AS completed FROM investor_plans WHERE investorID = ? AND plan_status = 'Completed'");
    $stmt->bind_param("i", $investorID);
    $stmt->execute();
    $paymentCheck = $stmt->get_result()->fetch_assoc();

    if ($paymentCheck['completed'] == 1) {
        $stmt = $conn->prepare("UPDATE investors SET investor_status = 'Active' WHERE investorID = ?");
        $stmt->bind_param("i", $investorID);
        $stmt->execute();
    }

    // 6. Check for referrer
    $stmt = $conn->prepare("SELECT facilitatorID FROM investor_referrals WHERE investorID = ?");
    $stmt->bind_param("i", $investorID);
    $stmt->execute();
    $refResult = $stmt->get_result();

    if ($refResult->num_rows > 0) {
        $referrerID = $refResult->fetch_assoc()['facilitatorID'];
        processCommissions($referrerID, $amount, $conn);
    }

    // 7. Send emails and notifications
    sendUserEmail($name, $email, $plan);
    sendAdminNotifications($name, $conn);

    // 8. Delete receipt (file + DB)
    deleteReceipt($reference, $conn);
    
    // 9. Get pending payments
    $pendingInvestments = getPendingInvestmentTotal($conn);

    // 9. Finalize
    $conn->commit();
    $conn->close();
    echo json_encode(['Info' => 'Payment approved successfully', 'data' => ['total' => $pendingInvestments ]]);

} catch (Exception $e) {
    $conn->rollback();
    $conn->close();
    error_log("Payment approval error: " . $e->getMessage());
    echo json_encode(['Error' => 'A server error occurred. Please try again later.']);
}

// -------------------------- HELPER FUNCTIONS ---------------------------- //
function deleteReceipt($reference, $conn) {
    $stmt = $conn->prepare("SELECT receipt_filename FROM investor_receipts WHERE receipt_reference = ?");
    $stmt->bind_param("s", $reference);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) return;

    $receiptName = $result->fetch_assoc()['receipt_filename'];

    if (strpos($receiptName, 'https') !== false) {
        require_once 'cloudinary-delete.php';
        deleteCloudinaryFile($receiptName);
    } else {
        $filePath = "../../../documents/" . $receiptName;
        if (file_exists($filePath)) unlink($filePath);
    }

    $stmt = $conn->prepare("DELETE FROM investor_receipts WHERE receipt_reference = ?");
    $stmt->bind_param("s", $reference);
    $stmt->execute();
}

function processCommissions($facilitatorID, $amount, $conn) {
    if (isTopAdmin($facilitatorID)) return;

    $referral_earning = $amount * REFERRAL_PERCENT;
    $downline_earning = $amount * DOWNLINE_PERCENT;

    handleReferralEarnings($facilitatorID, $referral_earning, $conn);
    $facilitator = getInvestorDetails($facilitatorID, $conn);
    sendAffiliateEmail($facilitator['fullname'], $facilitator['email'], $referral_earning);
    createAffiliateNotification($facilitator['fullname'], $facilitator['email'], $referral_earning, $conn);

    $stmt = $conn->prepare("SELECT facilitatorID FROM investor_referrals WHERE investorID = ?");
    $stmt->bind_param("i", $facilitatorID);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $referrerID = $row['facilitatorID'] ?? null;

    if ($referrerID && !isTopAdmin($referrerID)) {
        handleDownlineEarnings($referrerID, $downline_earning, $conn);
        $upper = getInvestorDetails($referrerID, $conn);
        sendUpperlineEmail($upper['fullname'], $upper['email'], $downline_earning);
        createUpperlineNotification($upper['fullname'], $upper['email'], $downline_earning, $conn);
    }
}

function handleReferralEarnings($id, $amount, $conn) {
    updateWallet($id, $amount, 'wallet_tier_one', $conn);
    updateWallet($id, $amount, 'wallet_tier_one_backup', $conn);
}

function handleDownlineEarnings($id, $amount, $conn) {
    updateWallet($id, $amount, 'wallet_tier_two', $conn);
    updateWallet($id, $amount, 'wallet_tier_two_backup', $conn);
}

function updateWallet($id, $amount, $table, $conn) {
    $stmt = $conn->prepare("UPDATE $table SET wallet_amount = wallet_amount + ? WHERE investorID = ?");
    $stmt->bind_param("di", $amount, $id);
    $stmt->execute();
}

function getInvestorDetails($id, $conn) {
    $stmt = $conn->prepare("SELECT email, fullname FROM investors WHERE investorID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function sendUserEmail($name, $email, $plan) {
    $message = "
        Hi $name, <br>
        Your payment for <b>$plan</b> investment package on NairaQuiz was successful. <br> 
        <a href='https://nairaquiz.com/access'>Login to your dashboard</a>
    ";
    send_email('Payment Success', $email, $message);
}

function sendAffiliateEmail($name, $email, $amount) {
    $formatted = '₦' . number_format($amount, 2);
    $msg = "Congratulations, $name!<br>You earned a commission of <b>$formatted</b>.<br><a href='https://nairaquiz.com/access'>Login here</a>";
    send_email('Successful Referral', $email, $msg);
}

function createAffiliateNotification($name, $email, $amount, $conn) {
    $msg = "You earned a commission of ₦" . number_format($amount, 2) . " from a referral.";
    $stmt = $conn->prepare("INSERT INTO general_notifications (notification_details, notification_type, notification_receiver, notification_date, notification_status) VALUES (?, 'affiliate_commission', ?, NOW(), 'Unseen')");
    $stmt->bind_param("ss", $msg, $email);
    $stmt->execute();
}

function sendUpperlineEmail($name, $email, $amount) {
    $formatted = '₦' . number_format($amount, 2, '.', ',');
    $msg = "Congratulations, $name!<br>You earned a downline commission of <b>$formatted</b>.<br><a href='https://nairaquiz.com/access'>Login here</a>";
    send_email('Downline Earning', $email, $msg);
}

function createUpperlineNotification($name, $email, $amount, $conn) {
    $details = "You earned ₦" . number_format($amount, 2, '.', ',') . " from a downline.";
    $stmt = $conn->prepare("INSERT INTO general_notifications (notification_details, notification_type, notification_receiver, notification_date, notification_status) VALUES (?, 'downline_commission', ?, NOW(), 'Unseen')");
    $stmt->bind_param("ss", $details, $email);
    $stmt->execute();
}

function sendAdminNotifications($name, $conn) {
    $note = "New investor, $name, registered on the site";
    $stmt = $conn->prepare("SELECT email FROM users WHERE user_type = 'Admin'");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $adminEmail = $row['email'];
        $insert = $conn->prepare("INSERT INTO general_notifications (notification_details, notification_type, notification_receiver, notification_date, notification_status) VALUES (?, 'investor_registration', ?, NOW(), 'Unseen')");
        $insert->bind_param("ss", $note, $adminEmail);
        $insert->execute();
    }
}

function isTopAdmin($id) {
    return in_array($id, json_decode(TOP_LEVEL_ADMINS));
}

function getPendingInvestmentTotal($conn) {
    $status = 'Pending';
    $stmt = $conn->prepare("SELECT SUM(plan_amount) AS total_amount FROM investor_plans WHERE plan_status = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return "₦0.00";
    }

    $stmt->bind_param("s", $status);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return "₦0.00";
    }

    $stmt->bind_result($total_amount);
    $stmt->fetch();
    $stmt->close();

    $total_amount = $total_amount ?? 0;
    return "&#x20A6;" . number_format($total_amount, 2, '.', ',');
}

?>