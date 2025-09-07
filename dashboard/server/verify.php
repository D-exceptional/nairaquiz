<?php
// paystack_verify.php

include "conn.php";
include "mailer.php";

$secretKey = "sk_live_8ff7eaf31a9aad6d301426780bbba849abbe9484"; // Replace with your actual Paystack secret key
$date = date('d/m/Y H:i');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $reference = $_POST['reference'];

    $url = "https://api.paystack.co/transaction/verify/" . $reference;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $secretKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //execute post
    $response = curl_exec($ch);
    $err = curl_error($ch);

    if($err){
        //there was an error contacting Paystack API
        $data = array(
            'Info' => 'Error connecting to Paystack API',
            'details' => array('error' => 'Curl returned the following error while connecting to Paystack API: ' . $err)
        );
    }

    $transaction = json_decode($response);
    if(!$transaction->data && !$transaction->data->status){
        // there was an error from the API
        $data = array(
        'Info' => 'Paystack API error ocuured',
        'details' => array('error' => 'Paystack API error occured while verifying transaction')
        );
    }
    else{
        //Obtain details
        $amountTotal = $transaction->data->amount;
        $referenceString = $transaction->data->reference;
        //Get userID and paid amount
        $sql = mysqli_query($conn, "SELECT fund_amount, userID FROM wallet_fund WHERE fund_txref = '$reference'");
        $row = mysqli_fetch_assoc($sql);
        $userID = $row['userID'];
        $fund_amount = $row['fund_amount'];
        //Get fullname
        $sql = mysqli_query($conn, "SELECT fullname FROM users WHERE userID = '$userID'");
        $row = mysqli_fetch_assoc($sql);
        $fullname = $row['fullname'];
        //Get sums
        $sql = mysqli_query($conn, "SELECT wallet_amount FROM wallet WHERE userID = '$userID'");
        $row = mysqli_fetch_assoc($sql);
        $wallet_amount = $row['wallet_amount'];
        $new_balance = $wallet_amount + $fund_amount;
        //Format balance
        $top_up_amount = "&#x20A6" . number_format($fund_amount, 2, '.', ',');
        $total_amount = "&#x20A6" . number_format($new_balance, 2, '.', ',');
        //Update wallet
        mysqli_query($conn, "UPDATE wallet SET wallet_amount = '$new_balance' WHERE userID = '$userID'");
        //Update history
        mysqli_query($conn, "UPDATE wallet_fund SET fund_status = 'Completed' WHERE fund_txref = '$reference'");
        //Send email
        $link = 'https://nairaquiz.com/login';
        $text = 'Login';
        $subject = "Successful Wallet Funding";
        $message = "
                    Hi <b>$fullname</b>, <br>
                    You have succesfully funded <b>$top_up_amount</b> to your wallet. <br>
                    You can now take more live quiz via your dashboard. <br>
                    Voila!
                    <br>
                    <br>
                    <a href='$link' target='_blank'><b>$text</b></a>
                ";
        send_email($subject, $email, $message);
        //Create notification
        $notification_title = 'funded wallet';
        $notification_details = "Hi, $fullname! <br> You have successfully funded your wallet with <b>$top_up_amount</b> <br> You can now take more quiz!";
        $notification_type = 'wallet_fund';
        $notification_date = date('Y-m-d H:i:s');
        $notification_status = 'Unseen';
        //Create notification
        mysqli_query($conn, "INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_receiver, notification_date, notification_status) VALUES ('$notification_title', '$notification_details', '$notification_type', '$email', '$notification_date', '$notification_status')");
        //Prepare response
        $data = array(
            'Info' => 'Payment verified',
            'details' => array('message' => "Your payment of <b>$top_up_amount</b> was successful. Kindly check your mailbox for more info.")
        );
    }
}
else{
    //Prepare response
    $data = array(
        'Info' => 'Invalid request',
        'details' => array('error' => 'The request method is invalid, therefore cannot proceed')
    );
}

$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);
exit();
?>
