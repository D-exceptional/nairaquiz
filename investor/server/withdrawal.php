<?php 
require 'session.php'; // Assumes $userID, $email, $fullname, and $conn are defined here
require 'mailer.php';

$response = ['Info' => 'Unknown error occurred'];

// Validate and sanitize POST inputs
$amount = $_POST['amount'] ?? null;
$account = $_POST['account'] ?? null;
$bank = $_POST['bank'] ?? null;
$narration = $_POST['narration'] ?? null;
$balance = $_POST['balance'] ?? null;
$type = $_POST['type'] ?? null;

if (empty($amount) || empty($account) || empty($bank) || empty($narration) || empty($balance)) {
    $response['Info'] = "Some fields are empty.";
    echo json_encode($response, JSON_FORCE_OBJECT);
    exit;
}

// Additional validation for numeric values
if (!is_numeric($amount) || !is_numeric($balance)) {
    $response['Info'] = "Invalid amount or balance.";
    echo json_encode($response, JSON_FORCE_OBJECT);
    exit;
}

$reference = generateReference();
$date = date('Y-m-d H:i:s');
$status = 'Pending';

// Start transaction
$conn->begin_transaction();

try {
    // Insert withdrawal request
    $stmt = $conn->prepare("INSERT INTO investment_withdrawal (withdrawal_amount, withdrawal_account, withdrawal_bank, withdrawal_date, withdrawal_status, withdrawal_reference, withdrawal_narration, investorID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("disssssi", $amount, $account, $bank, $date, $status, $reference, $narration, $investorID);
    $stmt->execute();
    $stmt->close();

    // Prepare message
    $subject = "Withdrawal Request";
    $message = "
        Hi, $fullname!<br>
        You have successfully placed a withdrawal request for <b>&#x20A6;" . number_format($amount, 2) . "</b>.<br>
        Your payout should be in your bank account soon.<br>
        Best wishes from the NairaQuiz team!<br><br>
        <a href='https://nairaquiz.com/login' target='_blank'><b>Track Request</b></a>
    ";

    switch ($type) {
        case 'Tier One':
            // Check if wallet_savings record exists
            $stmt = $conn->prepare("SELECT wallet_amount FROM wallet_tier_one WHERE investorID = ?");
            $stmt->bind_param("i", $investorID);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->close();

                // Update wallet_savings balance
                $updateStmt = $conn->prepare("UPDATE wallet_tier_one SET wallet_amount = ? WHERE investorID = ?");
                $updateStmt->bind_param("di", $balance, $investorID);
                $updateStmt->execute();
                $updateStmt->close();

                $conn->commit();

                $response['Info'] = "Withdrawal request placed successfully";

                // Send confirmation email
                send_email($subject, $email, $message);

            } else {
                $stmt->close();
                $conn->rollback();
                $response['Info'] = "Withdrawal request failed: No savings record found.";
            }
        break;
        case 'Tier Two':
            // Check if wallet_savings record exists
            $stmt = $conn->prepare("SELECT wallet_amount FROM wallet_tier_two WHERE investorID = ?");
            $stmt->bind_param("i", $investorID);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->close();

                // Update wallet_savings balance
                $updateStmt = $conn->prepare("UPDATE wallet_tier_two SET wallet_amount = ? WHERE investorID = ?");
                $updateStmt->bind_param("di", $balance, $investorID);
                $updateStmt->execute();
                $updateStmt->close();
                
                $conn->commit();

                $response['Info'] = "Withdrawal request placed successfully";

                // Send confirmation email
                send_email($subject, $email, $message);

            } else {
                $stmt->close();
                $conn->rollback();
                $response['Info'] = "Withdrawal request failed: No savings record found.";
            }
        break;
        case 'Tier Three':
            // Check if wallet_savings record exists
            $stmt = $conn->prepare("SELECT wallet_amount FROM wallet_tier_three WHERE investorID = ?");
            $stmt->bind_param("i", $investorID);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->close();

                // Update wallet_savings balance
                $updateStmt = $conn->prepare("UPDATE wallet_tier_three SET wallet_amount = ? WHERE investorID = ?");
                $updateStmt->bind_param("di", $balance, $investorID);
                $updateStmt->execute();
                $updateStmt->close();
                
                $conn->commit();

                $response['Info'] = "Withdrawal request placed successfully";

                // Send confirmation email
                send_email($subject, $email, $message);

            } else {
                $stmt->close();
                $conn->rollback();
                $response['Info'] = "Withdrawal request failed: No savings record found.";
            }
        break;
        
    }

} catch (Exception $e) {
    $conn->rollback();
    $response['Info'] = "Withdrawal request failed: " . $e->getMessage();
}

echo json_encode($response, JSON_FORCE_OBJECT);
$conn->close();
exit();

// Helper functions here
function generateReference()
{
    return 'TRF_' . bin2hex(random_bytes(10));
}
?>
