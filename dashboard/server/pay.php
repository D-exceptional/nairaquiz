<?php

require 'session.php'; // Assumes $userID and $email are defined here
require 'mailer.php';

// Set the time zone to Africa/Lagos
date_default_timezone_set('Africa/Lagos');

// Initialize response array
$response = array();

// Get parameters
$amount = $_POST['amount'] ?? null;
$currency = $_POST['currency'] ?? null;

if ($amount && $currency) {
    // Generate a unique transaction reference
    $reference = generateReference();
    $date = date('Y-m-d H:i:s');
    $total_payment = $currency . $amount;

    // Admin emails
    $admin_emails = ['chukwuebukaokeke09@gmail.com', 'krankstephen20@gmail.com'];
    $walletID = getOrCreateWallet($userID, $currency);

    if ($walletID) {
        if (isset($_FILES['receipt']) && !empty($_FILES['receipt'])) {
            $targetDir = '../../documents/';
            $img_name = $_FILES['receipt']['name'];
            $img_type = $_FILES['receipt']['type'];
            $tmp_name = $_FILES['receipt']['tmp_name'];
            $img_ext = pathinfo($img_name, PATHINFO_EXTENSION);
            $extensions = ['jpeg', 'png', 'jpg'];
            // Set new image name
            $new_img_name = $reference . '.' . $img_ext;

            // Check extensions
            if (in_array($img_ext, $extensions)) {
                // Upload image
                if (move_uploaded_file($tmp_name, $targetDir . $new_img_name)) {
                    // Insert fund record
                    $stmt = $conn->prepare('INSERT INTO wallet_fund (fund_amount, fund_date, fund_status, fund_txref, userID, walletID) VALUES (?, ?, ?, ?, ?, ?)');
                    $fund_status = 'Pending';
                    $stmt->bind_param('dsssii', $amount, $date, $fund_status, $reference, $userID, $walletID);
                    if ($stmt->execute()) {
                      // Save receipt file path
                      $stmt = $conn->prepare('INSERT INTO wallet_fund_receipt (receipt_image, receipt_name, receipt_date, userID) VALUES (?, ?, ?, ?)');
                      $stmt->bind_param('sssi', $new_img_name, $reference, $date, $userID);
                      $stmt->execute();

                      // Send payment verification message to affiliate
                      $subject = 'Payment Request Initiated';
                      $link = 'https://nairaquiz.com/login';
                      $text = 'Visit Dashboard';
                      $message = "
                        Hi, $fullname!  <br>
                        We have successfully received your payment request on NairaQuiz. <br>
                        Your payment of <b>$total_payment</b> is currently under verification and we will respond once everything is verified. <br>
                        Expect to hear from us within the next one hour.  <br>
                        Best wishes from the NairaQuiz team!<br><br>
                        <a href='$link' target='_blank'><b>$text</b></a>
                      ";
                      send_email($subject, $email, $message);

                      // Send payment notification message to admin
                      $admin_subject = 'New Payment Initiated';
                      $admin_message = "
                        Hello Admin, <br>
                        We are thrilled to inform you that a user just made payment on NairaQuiz.  <br>
                        Ensure to verify the payment and take necessary actions. <br>
                        Here are the details of the user: <br>
                        <center>
                          Fullname: <b>$fullname</b><br>
                          Amount paid: <b>$total_payment</b><br>
                          Reference: <b>$reference</b><br>
                          Date: <b>$date</b><br>
                        </center>
                        <br>
                        <a href='https://nairaquiz.com/admin/' target='_blank'><b>Visit Dashboard</b></a>
                      ";
                      
                      foreach ($admin_emails as $address) {
                        send_email($admin_subject, $address, $admin_message);
                      }

                      $response = array('Info' => 'Payment request received successfully');
                    } else {
                        $response = array('Info' => 'An error occurred');
                    }
                } else {
                    $response = array('Info' => 'Error occurred while uploading file');
                }
            } else {
                $response = array('Info' => 'Receipt must either be a .jpg, .jpeg or .png file');
            }
        } else {
            $response = array('Info' => 'Upload a valid image file');
        }
    } else {
        $response = array('Info' => 'Failed to create or retrieve wallet');
    }
} else {
    $response = array('Info' => 'Some fields are empty');
}

// Return response as JSON
echo json_encode($response);

// Close database connection
mysqli_close($conn);

// Function to generate a random alphanumeric code for the payment
function generateReference($length = 20) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $code = '';
  for ($i = 0; $i < $length; $i++) {
    $code .= $characters[rand(0, strlen($characters) - 1)];
  }
  return 'TRF_' . $code;
}

// Function to get or create a wallet for the user
function getOrCreateWallet($userID, $currency) {
  global $conn;
  $stmt = $conn->prepare('SELECT * FROM wallet WHERE userID = ?');
  $stmt->bind_param('i', $userID);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows === 0) {
    // Create a new wallet
    $wallet_amount = 0.1;
    $wallet_status = 'Active';
    $account_number = '0000000000';
    $bank = 'null';
    $recipient_code = 'Not available';
    $stmt = $conn->prepare('INSERT INTO wallet (wallet_amount, wallet_currency, wallet_status, account_number, bank, bank_code, recipient_code, userID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('dssssssi', $wallet_amount, $currency, $wallet_status, $account_number, $bank, $bank, $recipient_code, $userID);
    if ($stmt->execute()) {
      return $conn->insert_id;
    }
  } else {
    $row = $result->fetch_assoc();
    return $row['walletID'];
  }
  return null;
}

?>
