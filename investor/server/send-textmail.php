<?php
     require 'session.php';
     //Set the time zone to AFrica
     date_default_timezone_set("Africa/Lagos");

     $data = array();

     $recipient = mysqli_real_escape_string($conn, $_POST['recipient']);
     $subject = mysqli_real_escape_string($conn, $_POST['subject']);
     $message = mysqli_real_escape_string($conn, $_POST['message']);
     $processedMessage = formatMessage($message);
     $date = date('Y-m-d');
     $time = date('H:i');
     $type = 'Text';
     $filename = 'null';
     $extension = 'null';
     
     if(!empty($recipient) && !empty($subject) && !empty($message)){
          $stmt = $conn->prepare("INSERT INTO investor_mailbox (mail_type, mail_subject, mail_sender, mail_receiver, mail_date, mail_time, mail_message, mail_filename, mail_extension) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
          $stmt->bind_param("sssssssss", $type, $subject, $fullname, $recipient, $date, $time, $processedMessage, $filename, $extension);
          if ($stmt->execute()) {
               $data = array('Info' => 'Message sent successfully');
          } else {
               $data = array('Info' => 'Error sending message');
          }
     }
     else{ 
          $data = array('Info' => 'Some fields are empty'); 
     } 

     $encodedData = json_encode($data, JSON_FORCE_OBJECT);
     echo $encodedData;
     mysqli_close($conn);

     // Format message 
     function formatMessage($text) {
          $text = str_replace(["\r\n", "\r", "\n", "\\r\\n", "\\r", "\\n"], "\n", $text);
          $text = stripslashes(rtrim($text, '\\'));
          $text = str_replace("'", "&#8217;", $text);
          $text = preg_replace('/\s+/', ' ', $text);
          return nl2br($text);
      }
?>
