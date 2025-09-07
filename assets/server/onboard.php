<?php
require 'conn.php';
require 'mailer.php';

date_default_timezone_set("Africa/Lagos");

header('Content-Type: application/json');

$response = [];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

// Sanitize and validate inputs
$fields = ['name', 'email', 'contact', 'country', 'password', 'plan', 'amount', 'roi', 'id', 'currency', 'code'];
$data = [];
foreach ($fields as $field) {
    $data[$field] = trim($_POST[$field] ?? '');
}

if (in_array('', $data, true)) {
    echo json_encode(['status' => 'error', 'message' => 'Some fields are empty']);
    exit();
}

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
    exit();
}

if (!is_numeric($data['amount']) || floatval($data['amount']) <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid amount']);
    exit();
}

$sql = $conn->prepare("SELECT email FROM investors WHERE email = ?");
$sql->bind_param('s', $data['email']);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'This email is already registered']);
    exit();
}

if (!isset($_FILES['receipt']) || empty($_FILES['receipt']['tmp_name'])) {
    echo json_encode(['status' => 'error', 'message' => 'Upload a valid receipt file']);
    exit();
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $_FILES['receipt']['tmp_name']);
finfo_close($finfo);

$allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
$img_ext = strtolower(pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION));
$allowedExts = ['jpeg', 'jpg', 'png', 'pdf'];

if (!in_array($mime, $allowedMimes) || !in_array($img_ext, $allowedExts)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid file type']);
    exit();
}

$reference = generatePaymentReference();
$directory = 'documents';
$serverDir = __DIR__;
$targetDir = dirname(dirname($serverDir)) . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR;
$new_img_name = $reference . '.' . $img_ext;

if (!move_uploaded_file($_FILES['receipt']['tmp_name'], $targetDir . $new_img_name)) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to upload receipt']);
    exit();
}

$conn->begin_transaction();
try {
    $package = ucwords($data['plan']);
    $fullname = ucwords($data['name']);
    $contact = $data['code'] . substr($data['contact'], 1);
    $hashPassword = password_hash($data['password'], PASSWORD_BCRYPT);
    $profile = 'None';
    $status = 'Pending';
    $date = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO investors (investor_profile, fullname, email, contact, country, investor_password, investor_status, created_on, channel) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $channel = 'Admin';
    $stmt->bind_param('sssssssss', $profile, $fullname, $data['email'], $contact, $data['country'], $hashPassword, $status, $date, $channel);
    $stmt->execute();
    $investorID = $conn->insert_id;

    $stmt = $conn->prepare("INSERT INTO investor_receipts (receipt_amount, receipt_filename, receipt_reference, receipt_status, receipt_date, investorID) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("dssssi", $data['amount'], $new_img_name, $reference, $status, $date, $investorID);
    $stmt->execute();

    $stmt = $conn->prepare("INSERT INTO investor_referrals (facilitatorID, investorID, referral_date) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $data['id'], $investorID, $date);
    $stmt->execute();

    $stmt = $conn->prepare("INSERT INTO investor_finance (account_number, bank_name, currency_type, investorID) VALUES (0, 'None', ?, ?)");
    $stmt->bind_param('si', $data['currency'], $investorID);
    $stmt->execute();

    $updated = 'None';
    $stmt = $conn->prepare("INSERT INTO investor_plans (plan_name, plan_amount, plan_roi, plan_reference, plan_status, plan_created, plan_updated, investorID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sdsssssi', $package, $data['amount'], $data['roi'], $reference, $status, $date, $updated, $investorID);
    $stmt->execute();

    $wallet_amount = 0;
    $wallets = ['wallet_tier_one', 'wallet_tier_one_backup', 'wallet_tier_two', 'wallet_tier_two_backup', 'wallet_tier_three', 'wallet_tier_three_backup'];
    foreach ($wallets as $wallet) {
        $stmt = $conn->prepare("INSERT INTO $wallet (wallet_amount, investorID) VALUES (?, ?)");
        $stmt->bind_param("di", $wallet_amount, $investorID);
        $stmt->execute();
    }

    // Commit transaction
    $conn->commit();

    // Send notifications
    sendWelcomeEmail($fullname, $data['email'], $data['amount'], $package);
    sendUserTypeNotification($fullname, $data['email'], $contact, $data['amount'], $package, $reference, $date, 'Admin', 'https://mrsamase.com/admin/', 'Admin');

    echo json_encode(['status' => 'success', 'message' => 'Payment Request Received. Check your email for more info.']);
} catch (Exception $e) {
    $conn->rollback();
    error_log("Registration error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'A server error occurred. Try again.']);
}

function generatePaymentReference() {
    return strtoupper("PAY" . time() . bin2hex(random_bytes(4)));
}

function sendWelcomeEmail($fullname, $email, $amount, $plan) {
    $formattedAmount = '₦' . number_format($amount, 2);
    $subject = 'Successful Application';
    $message = "Hi <b>$fullname</b>,<br>We have received your application for the <b>$plan</b> investment package on NairaQuiz.<br> Your payment of <b>$formattedAmount</b> is under review.<br> Expect to hear from us within the next one hour <br> Best wishes from the NairaQuiz team! <br><a href='https://nairaquiz.com/access'><b>Welcome onboard!</b></a>";
    send_email($subject, $email, $message);
}

function sendUserTypeNotification($fullname, $email, $contact, $amount, $plan, $reference, $date, $userType, $dashboard, $greeting) {
    global $conn;
    $formattedAmount = '₦' . number_format($amount, 2);
    $subject = 'New Investment Payment';
    $message = "
        Hello $greeting,<br> 
        A new investor just registered for the <b>$plan</b> package on NairaQuiz.<br>
        Kindly review the review the registration and take necessary actions.<br>
        Here are the details of the investor:<br><br>
        
        Fullname: <b>$fullname</b><br>
        Email: <b>$email</b><br>
        Contact: <b>$contact</b><br>
        Package: <b>$plan</b><br>
        Amount: <b>$formattedAmount</b><br>
        Reference: <b>$reference</b><br>
        Date: <b>$date</b>
        <br>
        <a href='$dashboard'><b>Visit Dashboard</b></a>
    ";

    $emails = [];
    $stmt = $conn->prepare("SELECT email FROM users WHERE user_type = ?");
    $stmt->bind_param("s", $userType);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $emails[] = $row['email'];
    }

    if (strtolower($userType) === 'admin') {
        $emails[] = 'chukwuebukaokeke09@gmail.com';
    }

    foreach ($emails as $mail) {
        send_email($subject, $mail, $message);
    }
}
?>
