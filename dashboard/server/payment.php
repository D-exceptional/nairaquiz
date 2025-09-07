<?php

require 'conn.php';

$userID = mysqli_real_escape_string($conn, $_POST['id']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$amount = mysqli_real_escape_string($conn, $_POST['amount']);  
$currency = mysqli_real_escape_string($conn, $_POST['currency']);
$db_amount = $amount / 100;
$date = date('Y-m-d H:i:s');
$walletID = "";

//Check wallet existence
$sql = mysqli_query($conn, "SELECT * FROM wallet WHERE userID = '$userID'");
if(mysqli_num_rows($sql) === 0){
  $wallet_amount = 0.1;
  $wallet_currency = $currency;
  $wallet_status = "Active";
  $account_number = 0000000000;
  $bank = "null";
  $bank_code = "null";
  $recipient_code = "Not available";
  //Create wallet
  $create_wallet = mysqli_query($conn, "INSERT INTO wallet (wallet_amount, wallet_currency, wallet_status, account_number, bank, bank_code, recipient_code, userID) VALUES ('$wallet_amount', '$wallet_currency', '$wallet_status', '$account_number', '$bank', '$bank_code', '$recipient_code', '$userID')");
  if ($create_wallet === true) {
    sleep(1);
    //Get wallet ID
    $query = mysqli_query($conn, "SELECT * FROM wallet WHERE userID = '$userID'");
    if(mysqli_num_rows($query) > 0){
      $row = mysqli_fetch_assoc($query);
      $walletID = $row['walletID'];
    }
    else{
      $data = array(
        'Info' => 'Wallet not found',
        'details' => array('error' => 'No wallet was found for user')
      );
    }
  } else {
    $data = array(
      'Info' => 'Error creating wallet',
      'details' => array('error' => 'An error occured while wallet was being created')
    );
  }
}
else{
  //Get wallet ID
  $query = mysqli_query($conn, "SELECT * FROM wallet WHERE userID = '$userID'");
  if(mysqli_num_rows($query) > 0){
    $row = mysqli_fetch_assoc($query);
    $walletID = $row['walletID'];
  }
  else{
    $data = array(
      'Info' => 'Wallet not found',
      'details' => array('error' => 'No wallet was found for user')
    );
  }
}

//Initiate connection to Paystack to get access code and reference

$url = "https://api.paystack.co/transaction/initialize";
$fields = [
  'email' => $email,
  'amount' => $amount
];
$fields_string = http_build_query($fields);
//open connection
$ch = curl_init();
//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, true);
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Authorization: Bearer sk_live_8ff7eaf31a9aad6d301426780bbba849abbe9484",
  "Cache-Control: no-cache",
));
//So that curl_exec returns the contents of the cURL; rather than echoing it
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
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
if(!$transaction->data && !$transaction->data->access_code){
  // there was an error from the API
  $data = array(
    'Info' => 'Paystack API error ocuured',
    'details' => array('error' => 'Paystack API error occured while initializing transaction')
  );
}
else{
  //Obtain details
  $authorization_url = $transaction->data->authorization_url;
  $access_code = $transaction->data->access_code;
  $reference = $transaction->data->reference;
  //Create payment record
  mysqli_query($conn, "INSERT INTO wallet_fund (fund_amount, fund_date, fund_status, fund_txref, userID, walletID) VALUES ('$db_amount', '$date', 'Pending', '$reference', '$userID', '$walletID')");
  //Prepare response
  $data = array(
    'Info' => 'Payment code obtained',
    'details' => array('access_code' => $access_code, 'reference' => $reference)
  );
}

$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);
exit();

?>