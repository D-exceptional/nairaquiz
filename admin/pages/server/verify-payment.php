<?php

require 'conn.php';

$data = array();

$id = mysqli_real_escape_string($conn, $_POST['id']);

$secret_key = 'sk_live_dd519ff2272708c948e5e92b4149029ab52328ca';

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => `https://api.flutterwave.com/v3/transfers/${id}`,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer $secret_key"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if($err){
  // there was an error contacting the rave API
   $data = array(
       'Info' => 'Error connecting to payment gateway',
       'details' => array( 'error' => $err )
  );
   $encodedData = json_encode($data, JSON_FORCE_OBJECT);
   echo $encodedData;
   exit();
}
else{
    $resp = json_decode($response);
    //Prepare data to send to front end
     $data = array(
      'Info' => 'Transaction details fetched successfully',
      'details' => array(
          'id' => $resp->data->id,
          'account' => $resp->data->account_number,
          'name' => $resp->data->full_name,
          'amount' => $resp->data->amount,
          'status' => $resp->data->status,
          'bank' => $resp->data->bank_name,
          'date' => $resp->data->created_at,
          'message' => $resp->data->complete_message,
          'fee' => $resp->data->fee,
          'currency' => $resp->data->currency
      )
   );
    $encodedData = json_encode($data, JSON_FORCE_OBJECT);
    echo $encodedData;
    exit();
}

?>