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
    $stmt = $conn->prepare("INSERT INTO withdrawals (payment_email, payment_amount, payment_account, payment_bank, payment_date, payment_status, payment_txref, payment_narration, userID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdssssssi", $email, $amount, $account, $bank, $date, $status, $reference, $narration, $userID);
    $stmt->execute();
    $stmt->close();

    // Check if wallet_savings record exists
    $stmt = $conn->prepare("SELECT wallet_amount FROM wallet_savings WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();

        // Update wallet_savings balance
        $updateStmt = $conn->prepare("UPDATE wallet_savings SET wallet_amount = ? WHERE userID = ?");
        $updateStmt->bind_param("di", $balance, $userID);
        $updateStmt->execute();
        $updateStmt->close();

        $conn->commit();

        // Send confirmation email
        $subject = "Withdrawal Request";
        $link = 'https://nairaquiz.com/login';
        $text = 'Track Request';
        $message = "
            Hi, $fullname!<br>
            You have successfully placed a withdrawal request for <b>&#x20A6;" . number_format($amount, 2) . "</b>.<br>
            Your payout should be in your bank account soon.<br>
            Best wishes from the NairaQuiz team!<br><br>
            <a href='$link' target='_blank'><b>$text</b></a>
        ";
        send_email($subject, $email, $message);

        // Admin emails
        $admin_emails = ['chukwuebukaokeke09@gmail.com', 'krankstephen20@gmail.com'];
        $admin_subject = 'New Withdrawal Initiated';
        $admin_message = "
            Hello Admin, <br>
            We are thrilled to inform you that a user just placed a withdrawal request on NairaQuiz.  <br>
            Ensure to verify the it and take necessary actions. <br>
            Here are the details of the user: <br>
            <center>
                Fullname: <b>$fullname</b><br>
                Amount requested: <b>" . number_format($amount, 2) . "</b><br>
                Reference: <b>$reference</b><br>
                Date: <b>$date</b><br>
            </center>
            <br>
            <a href='https://nairaquiz.com/admin/' target='_blank'><b>Visit Dashboard</b></a>
        ";
        
        foreach ($admin_emails as $address) {
            send_email($admin_subject, $address, $admin_message);
        }

        $response['Info'] = "Withdrawal request placed successfully";

    } else {
        $stmt->close();
        $conn->rollback();
        $response['Info'] = "Withdrawal request failed: No savings record found.";
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
