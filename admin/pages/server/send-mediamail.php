<?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;
    require '../../../vendor/autoload.php';
    require 'session.php';
    //Set the time zone to AFrica
    date_default_timezone_set("Africa/Lagos");
    mysqli_set_charset($conn, 'utf8');
    
    // Format message 
     function formatMessage($text) {
        $text = str_replace(["\r\n", "\r", "\n", "\\r\\n", "\\r", "\\n"], "\n", $text);
        $text = stripslashes(rtrim($text, '\\'));
        return nl2br($text);
    }

     //Functions for sending transfers data in chunks to Paystack
     function sendBulkMail($array) {
          global $conn;
          $save_date = date('Y-m-d H:i:s');
          // Split the array into batches of 10
          $batches = array_chunk($array, 10);
          //Total batches available
          $total_batches = count($batches);
          //Current batch
          $current_batch_index = 0;
          // Iterate through each batch
          foreach ($batches as $batch) {
               // Process each object in the batch and construct your SQL query
               $sqlValues = '';
               $queryValues = '';
               foreach ($batch as $item) {
                    $formattedMessage = formatMessage($item['mail_message']);
                    $sqlValues .= "('{$item['mail_type']}', '{$item['mail_subject']}', '{$item['mail_sender']}', '{$item['mail_receiver']}', '{$item['mail_date']}', '{$item['mail_time']}', '" . mysqli_real_escape_string($conn, $formattedMessage) . "',  '{$item['mail_filename']}','{$item['mail_extension']}'),";
                    $queryValues .= "('An incoming mail was received', 'incoming_mail','{$item['mail_receiver']}', '$save_date', 'Unseen'),";
                    //Send mail
                    try {
                     $mail = new PHPMailer(true);
                     $mail->SMTPDebug = 0; // change this to 0 later
                     $mail->isSMTP();      // Set mailer to use SMTP
                     $mail->Host = 'premium294.web-hosting.com';  // Specify main and backup SMTP servers
                     $mail->SMTPAuth = true;   // Enable SMTP authentication
                     $mail->Username = 'admin@nairaquiz.com';  // SMTP username
                     $mail->Password = '@nairaQuiz2024'; // SMTP password
                     $mail->SMTPSecure = 'tls';  // Enable TLS encryption, `ssl` also accepted
                     $mail->Port = 587;   // TCP port to connect to
                     $mail->From = 'admin@nairaquiz.com'; //Change this later
                     $mail->FromName = 'NairaQuiz';
                     $mail->addAddress($item['mail_receiver']);  // Add a recipient, Name is optional
                     $mail->addAttachment($item['filePath']); //Attach the file to send
                     $mail->isHTML(true);   // Set email format to HTML
                     $mail->Subject = $item['mail_subject'];
                     $mail->Body = $formattedMessage;
                     //Add the PHPMailer line of code for attaching attachments below this comment
                     $mail->send();
                    }
                    catch (Exception $e) {
                     // $data = 'Mailer Error: ' . $mail->ErrorInfo;
                     $data = array('Info' => 'SMTP Mailer Error');
                     $encodedData = json_encode($data, JSON_FORCE_OBJECT);
                     echo $encodedData;
                     exit();
                    }
               }

                // Remove the trailing comma
               $sqlValues = rtrim($sqlValues, ',');
               // Construct the SQL query to insert the batch into the database
               $sql = "INSERT INTO mailbox (mail_type, mail_subject, mail_sender, mail_receiver, mail_date, mail_time, mail_message, mail_filename, mail_extension) VALUES $sqlValues";
               // Execute the SQL query
               mysqli_query($conn, $sql);

                //Create notification
               $queryValues = rtrim($queryValues, ',');
               $query = "INSERT INTO general_notifications (notification_details, notification_type, notification_receiver, notification_date, notification_status) VALUES $queryValues";
               // Execute the SQL query
               mysqli_query($conn, $query);
               
               // Increment the current batch index
               $current_batch_index++;

               // Check if this is the last batch
               if ($current_batch_index === $total_batches) {
                    // This is the last batch, generate a response and send it to the client
                    $data = array('Info' => 'Message sent successfully');
                    $encodedData = json_encode($data, JSON_FORCE_OBJECT);
                    echo $encodedData;
                    exit();
               }
               //Wait for 2 seconds before next batch
               sleep(2);
          }
     }

     $data = array();

     //Storage and notification array
     $mailArray = [];

     $recipients = $_POST['recipients'];
     $subject = mysqli_real_escape_string($conn, $_POST['subject']);
     $message = mysqli_real_escape_string($conn, $_POST['message']);
     $sender = mysqli_real_escape_string($conn, $_POST['sender']);
     $date = date('Y-m-d');
     $time = date('H:i');
     $type = 'Multimedia';
     
     if(!empty($sender) && !empty($subject) && !empty($message)){

          if(isset($_FILES['attachment']) && !empty($_FILES['attachment'])){ 

               $targetDir = "../../../attachments/";
               $filename = $_FILES['attachment']['name'];
               $filetype = $_FILES['attachment']['type'];
               $tmp_name = $_FILES['attachment']['tmp_name'];
               $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
               $extensions = ["jpeg", "png", "jpg", "pdf", "mp3", "mp4", "docx"];
               $filePath = $targetDir.$filename;

               if(in_array($file_ext, $extensions) === true){ 

                    if(move_uploaded_file($tmp_name, $targetDir.$filename)){

                         //Decode `$recipients` to get the object version of its data
                         $mail_receivers = json_decode($recipients, true);
                         //Loop through each object in `$mail_recipients` and populate the mail array
                         foreach($mail_receivers as $key => $value){
                              $fullname = $value['name'];
                              $email = $value['email'];    
                              //Mail array items
                              $mail_item = [
                                   "mail_type" => $type,
                                   "mail_subject" => $subject,
                                   "mail_sender" => $sender,
                                   "mail_receiver" => $email,
                                   "mail_date" => $date,
                                   "mail_time" => $time,
                                   "mail_message" => $message,
                                   "mail_filename" => $filename,
                                   "mail_extension" => $file_ext,
                                   "filePath" => $filePath
                              ];
                              //Queue up in array
                              array_push($mailArray, $mail_item);
                         }

                         //Send out emails
                         sendBulkMail($mailArray);

                    }else{ $data = array('Info' => 'Failed to upload attachment'); } 

               }else{ $data = array('Info' => 'Attachment must have either .jpg, .jpeg, .png, .pdf, .mp3, .mp4 or .docx extension'); } 

          }else{ $data = array('Info' => 'Please, upload a valid attachment'); }

     }else{ $data = array('Info' => 'All fields must be filled up'); } 

     $encodedData = json_encode($data, JSON_FORCE_OBJECT);
     echo $encodedData;
     mysqli_close($conn);

?>
