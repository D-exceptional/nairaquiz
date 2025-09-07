<?php
     
  // only a post with paystack signature header gets our attention
  if ((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST' ) || !array_key_exists('HTTP_X_PAYSTACK_SIGNATURE', $_SERVER) ) 
    exit();

    // Retrieve the request's body
    $input = @file_get_contents("php://input");

    define('PAYSTACK_SECRET_KEY', 'sk_live_8ff7eaf31a9aad6d301426780bbba849abbe9484');

    // validate event do all at once to avoid timing attack
    if($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !== hash_hmac('sha512', $input, PAYSTACK_SECRET_KEY))
    exit();

    //Acknowledge response
    http_response_code(200);

    // Get verification file
    require 'functions.php';

    // parse event (which is json string) as object and do something that will not take long with $event
    $event = json_decode($input);

    //Process data
    $event_type = $event->event;

    //Take actions based on the event type
    switch ($event_type) {
      case 'charge.dispute.create':
      case 'charge.dispute.remind':
      case 'charge.dispute.resolve':
        $data = $event->data;
        $refund_amount = $data->refund_amount;
        $status = $data->status;
        $customer = $data->customer; //Customer object
        $customer_email = $customer->email;
        $customer_fullname = $customer->first_name . ' ' . $customer->last_name;
        $customer_contact = $customer->phone;
        $transaction = $data->transaction; //Transaction object
        $transaction_status = $transaction->status; 
        $transaction_reference = $transaction->reference;
        $transaction_amount = $transaction->amount;
        $transaction_currency = $transaction->currency;
        $transaction_channel = $transaction->channel;
        $transaction_date = $transaction->paid_at;
        $message = $data->messages; //Message object
        $message_sender = $message->sender;
        $message_body = $message->body;
        //Do something here...like sending them a mail 
      break;
      case 'paymentrequest.pending':
        $data = $event->data;
        $amount = $data->amount;
        $status = $data->status;
        $currency = $data->currency;
        $customer = $data->customer;
        $code = $data->request_code;
        //Do something here...like sending them a mail 
      break;
      case 'paymentrequest.success':
        $data = $event->data;
        $amount = $data->amount;
        $status = $data->status;
        $currency = $data->currency;
        $customer = $data->customer;
        $code = $data->request_code;
        $notification = $data->notifications;
        $notification_time = $notification->sent_at;
        $notification_channel = $notification->channel;
        //Do something here...like sending them a mail 
      break;
      case 'refund.failed':
      case 'refund.processed':
        $data = $event->data;
        $amount = $data->amount;
        $status = $data->status;
        $currency = $data->currency;
        $customer = $data->customer; //Customer object
        $customer_email = $customer->email;
        $customer_fullname = $customer->first_name . ' ' . $customer->last_name;
        $transaction_reference = $data->transaction_reference;
        $refund_reference = $data->refund_reference;
        $processor = $data->processor;
        //Do something here...like sending them a mail 
      break;
      case 'refund.pending':
      case 'refund.processing':
        $data = $event->data;
        $amount = $data->amount;
        $status = $data->status;
        $currency = $data->currency;
        $customer = $data->customer; //Customer object
        $customer_email = $customer->email;
        $customer_fullname = $customer->first_name . ' ' . $customer->last_name;
        $transaction_reference = $data->transaction_reference;
        $processor = $data->processor;
        //Do something here...like sending them a mail 
      break;
      case 'charge.success':
        $data = $event->data;
        $status = $data->status;
        $reference = $data->reference;
        $amount = $data->amount;
        $time = $data->paid_at;
        $channel = $data->channel;
        $currency = $data->currency;
        $gateway_response = $data->gateway_response;
        $customer = $data->customer; //Customer object
        $customer_email = $customer->email;
        $customer_fullname = $customer->first_name . ' ' . $customer->last_name;
        $customer_code = $customer->customer_code;
        $authorization = $data->authorization; //Authorization object
        $authorization_code = $authorization->authorization_code; 
        $account_name = $authorization->account_name;
        $bank = $authorization->bank;
        $card_type = $authorization->card_type;
        $last4 = $authorization->last4;
        /************************ Check Transaction ***************************** */
        checkTransaction($reference, $currency);
      break;
      case 'transfer.success':
        $data = $event->data;
        $amount = $data->amount;
        $currency = $data->currency;
        $status = $data->status;
        $reference = $data->reference;
        $transfer_code = $data->transfer_code;
        $recipient = $data->recipient;   //Recipient object
        $recipient_name = $recipient->name;
        $recipient_type = $recipient->type;
        $recipient_account_number = $recipient->details->account_name;
        $recipient_bank_name = $recipient->details->bank_name;
        //Do something here...like sending them a mail 
      break;
      case 'transfer.failed':
        $data = $event->data;
        $amount = $data->amount;
        $currency = $data->currency;
        $status = $data->status;
        $reference = $data->reference;
        $transfer_code = $data->transfer_code;
        $recipient = $data->recipient;   //Recipient object
        $recipient_name = $recipient->name;
        $recipient_type = $recipient->type;
        $recipient_account_number = $recipient->details->account_name;
        $recipient_bank_name = $recipient->details->bank_name;
        //Do something here...like sending them a mail 
      break;
      case 'transfer.reversed':
        $data = $event->data;
        $amount = $data->amount;
        $currency = $data->currency;
        $status = $data->status;
        $reference = $data->reference;
        $transfer_code = $event->transfer_code;
        $recipient = $event->recipient;   //Recipient object
        $recipient_name = $recipient->name;
        $recipient_type = $recipient->type;
        $recipient_account_number = $recipient->details->account_name;
        $recipient_bank_name = $recipient->details->bank_name;
        //Do something here...like sending them a mail 
      break;
    }

  exit();
?>