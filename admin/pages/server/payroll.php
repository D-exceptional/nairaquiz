<?php

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get email sending file
require 'conn.php';
require 'functions.php';

// Set the time zone to Africa/Lagos
date_default_timezone_set("Africa/Lagos");

// Function to generate a random alphanumeric code
function generateReference($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return 'payout' . $code;
}

// Function to sanitize input data
function sanitizeData($data) {
    // Trim whitespace
    $data = trim($data);
    // Remove HTML and PHP tags
    $data = strip_tags($data);
    // Escape special characters to prevent SQL injection
    $data = addslashes($data);
    return $data;
}

// Function to handle errors gracefully
function handleErrors($error_message) {
    // Define the log file path
    $logFile = 'error_log';
    // Log error message to a file
    error_log("Error: " . $error_message, 3, $logFile);
    // Prepare the JSON response
    $message = array('Info' => $error_message);
    // Set the Content-Type header to application/json
    header('Content-Type: application/json');
    // Encode the message as JSON
    $json = json_encode($message, JSON_FORCE_OBJECT);
    // Check for JSON encoding errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON encoding error: " . json_last_error_msg(), 3, $logFile);
        $json = json_encode(array('Info' => 'An error occurred.'));
    }
    // Output the JSON response and terminate script execution
    echo $json;
    exit();
}

// Function to handle errors gracefully
function handleSuccess($success_message) {
    $message = array('Info' => $success_message);
    // Set the Content-Type header to application/json
    header('Content-Type: application/json');
    // Encode the message as JSON
    $json = json_encode($message, JSON_FORCE_OBJECT);
    // Output the JSON response and continue script execution
    echo $json;
}

// Function to initiate payment transfers
function initiateTransfer($array) {
    // Initialize curl handle
    $curl = curl_init();

    // Check if curl initialization was successful
    if (!$curl) {
        handleErrors("Failed to initialize cURL");
    }

    foreach (array_chunk($array, 10) as $batch) { //Adjust the number as you wish
        $paystack_array = [];
        $sqlValues = '';

        foreach ($batch as $item) {
            // Sanitize data before using it
            $item['email'] = sanitizeData($item['email']);
            $item['account'] = sanitizeData($item['account']);
            $item['bank'] = sanitizeData($item['bank']);
            $item['date'] = sanitizeData($item['date']);
            $item['status'] = sanitizeData($item['status']);
            $item['reference'] = generateReference();

            array_push($paystack_array, $item);
            $sqlValues .= "('{$item['email']}', '{$item['payout']}', '{$item['account']}', '{$item['bank']}', '{$item['date']}', '{$item['status']}', '{$item['reference']}'),";
        }

        $fields = [
            'currency' => "NGN",
            'source' => "balance",
            'transfers' => $paystack_array
        ];

        $fields_string = http_build_query($fields);
        $url = "https://api.paystack.co/transfer/bulk";

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer sk_live_dd519ff2272708c948e5e92b4149029ab52328ca",
            "Cache-Control: no-cache",
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        if ($error) {
            handleErrors("cURL Error: " . $error);
        } else {
            require 'conn.php';
            $sqlValues = rtrim($sqlValues, ',');
            $sql = "INSERT INTO transaction_payments (payment_email, payment_amount, payment_account, payment_bank, payment_date, payment_status, payment_txref) VALUES $sqlValues";
            $query = "INSERT INTO transaction_payments_backup (payment_email, payment_amount, payment_account, payment_bank, payment_date, payment_status, payment_txref) VALUES $sqlValues";

            // Execute SQL query
            $result = mysqli_query($conn, $sql);

            if (!$result) {
                handleErrors("MySQL Error: " . mysqli_error($conn));
            }
            
            //Back up
            //mysqli_query($conn, $query);
            
        }

        // Pause execution for 5 seconds
        sleep(5);
    }

    // Close cURL session
    curl_close($curl);
}

// Get incoming request data from JS
$beneficiaries = $_POST['beneficiaries'];

// Check if beneficiaries data is empty
if (empty($beneficiaries)) {
    handleErrors("No payment data received");
}

// Transfer arrays
$payoutArray = [];

// Decode incoming JSON data
$transfer_recipients = json_decode($beneficiaries);

// Check if JSON decoding was successful
if (!$transfer_recipients) {
    handleErrors("Failed to decode JSON data");
}

// Loop through each object in $transfer_recipients
foreach ($transfer_recipients as $value) {
    // Sanitize data before using it
    $fullname = sanitizeData($value->fullname);
    $email = sanitizeData($value->email);
    $account = sanitizeData($value->account);
    $bank = sanitizeData($value->bank);
    $amount = sanitizeData($value->amount);
    $currency = sanitizeData($value->currency);
    $recipient = sanitizeData($value->code);
    $reason = sanitizeData($value->reason);
    //$payday = sanitizeData($value->payday);
    $status = 'Completed'; //Change to `Pending` once we get a working online payment processor.
    $formatted_amount = $amount / 100;
    $payout_amount = $formatted_amount / 1000; //Remove this line once we get a working online payment processor.
    $date = date('Y-m-d H:i:s');

    // Transfer array items
    $transfer = [
        "amount" => $amount,
        "reason" => $reason,
        "recipient" => $recipient,
        "email" => $email,
        "payout" => $formatted_amount,
        "account" => $account,
        "bank" => $bank,
        "date" => $date,
        "status" => $status,
    ];

    // Queue up in array
    array_push($payoutArray, $transfer);
    
    //Update withdrawal status
    mysqli_query($conn, "UPDATE withdrawals SET withdrawal_status = 'Completed' WHERE withdrawal_email = '$email'");
    
    /*
    Send an email to the beneficiaries. 
    Remove these email code once we get a working online payment processor.
    Email should be sent to beneficiaies after via webhook trigger
    */

    $link = '#';
    $text = 'Enjoy';
    $subject = "Chromstack Payout";
    $message = "
                Congratulations &#128640;&#129392;, $fullname, <br>
                You have received a payment of <b>$$payout_amount</b> from Chromstack. <br>
                We ensure to reward everyone on our payroll for their efforts. <br>
                Have a lovely weekend.
                <br>
                <br>
                <a href='$link' target='_blank'><b>$text</b></a>
            ";
    if ($email === "okekeebuka928@gmail.com") {
        send_email($subject, "chukwuebukaokeke09@gmail.com", $message);
    } else {
        send_email($subject, $email, $message);
    }
    
    //Create notification
    $notification_title = 'received a payment';
    $notification_details = "Congratulations, $fullname! <br> You have received a payment of <b>$$payout_amount</b> from Chromstack <br> Have a lovely weekend";
    $notification_type = 'weekly_payout';
    $notification_name = $fullname;
    $notification_date = date('Y-m-d H:i:s');
    $notification_status = 'Unseen';
    //Create notification
    mysqli_query($conn, "INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_name, notification_receiver_email, notification_date, notification_status) 
    VALUES ('$notification_title', '$notification_details', '$notification_type', '$fullname', '$email', '$notification_date', '$notification_status')");
    
}

// Check if there is payment data available
if (count($payoutArray) > 0) {
    initiateTransfer($payoutArray);
    //Send a response
    handleSuccess('Transfers queued successfully');
} else {
    handleErrors("No payment data available");
}

?>
