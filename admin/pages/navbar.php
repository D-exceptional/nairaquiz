<?php

   //Set the time zone to AFrica
   date_default_timezone_set("Africa/Lagos");

   //Function definition

   function timeAgo($time_ago)  //The argument $time_ago is in timestamp (Y-m-d H:i:s) format.
   {
      $time_ago = strtotime($time_ago);
      $cur_time = time();
      $time_elapsed = $cur_time - $time_ago;
      $seconds = $time_elapsed;
      $minutes = round($time_elapsed / 60 );
      $hours = round($time_elapsed / 3600);
      $days = round($time_elapsed / 86400 );
      $weeks = round($time_elapsed / 604800);
      $months = round($time_elapsed / 2600640 );
      $years = round($time_elapsed / 31207680 );
      // Seconds
      if($seconds <= 60){
            return "Just now";
      }
      //Minutes
      else if($minutes <=60){
            if($minutes==1){
               return "1 minute ago";
            }
            else{
               return "$minutes minutes ago";
            }
      }
      //Hours
      else if($hours <=24){
            if($hours==1){
               return "1 hour ago";
            }else{
               return "$hours hours ago";
            }
      }
      //Days
      else if($days <= 7){
            if($days==1){
               return "Yesterday";
            }else{
               return "$days days ago";
            }
      }
      //Weeks
      else if($weeks <= 4.3){
            if($weeks==1){
               return "1 week ago";
            }else{
               return "$weeks weeks ago";
            }
      }
      //Months
      else if($months <=12){
            if($months==1){
               return "1 month ago";
            }else{
               return "$months months ago";
            }
      }
      //Years
      else{
            if($years==1){
               return "1 year ago";
            }else{
               return "$years years ago";
            }
      }
   }
?>

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button">
         <?php
            $parts = explode(' ', $fullname);
            $firstName = $parts[0];
            $hour = date( "G" ); 
            if ( $hour >= 0 && $hour < 12 ) { 
               echo "<p class='d-block' style='overflow: hidden !important;white-space: nowrap !important;text-overflow: ellipsis !important;'>Good morning, $firstName</p>";
            } elseif ( $hour >= 12 && $hour < 18 ) { 
                  echo "<p class='d-block' style='overflow: hidden !important;white-space: nowrap !important;text-overflow: ellipsis !important;'>Good afternoon, $firstName</p>";
            } elseif ( $hour >= 18 && $hour <= 23 ) { 
               echo "<p class='d-block' style='overflow: hidden !important;white-space: nowrap !important;text-overflow: ellipsis !important;'>Good evening, $firstName</p>";
            } 
          ?>
        </a>
      </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" id='page-search' placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li>
      <!-- Notifications Dropdown Menu -->
            <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#" id="notification-link">
          <i class="far fa-bell"></i>
           <?php 
               $notification_count = mysqli_query($conn, "SELECT * FROM general_notifications WHERE notification_receiver = '$email' AND notification_status = 'Unseen' GROUP BY notification_type");
               if (mysqli_num_rows($notification_count) > 0) {
                  $total = mysqli_num_rows($notification_count);
                  echo "<span class='badge badge-danger navbar-badge'>$total</span>";
               }
            ?>  
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
         <span class="dropdown-item dropdown-header">
            <?php 
               $notification_count = mysqli_query($conn, "SELECT * FROM general_notifications WHERE notification_receiver = '$email' AND notification_status = 'Unseen' GROUP BY notification_type");
               if (mysqli_num_rows($notification_count) > 0) {
                  $total = mysqli_num_rows($notification_count);
                  echo "<span class='dropdown-item dropdown-header'>$total New notifications</span>";
               }
               else{
                   echo "<span class='dropdown-item dropdown-header'>No new notifications</span>";
               }
            ?>  
          </span>
        <!-- Check incoming mails -->
        <?php 
            $incoming_mail_count = mysqli_query($conn, "SELECT * FROM general_notifications WHERE notification_type = 'incoming_mail' AND notification_receiver = '$email' AND notification_status = 'Unseen'");
            if (mysqli_num_rows($incoming_mail_count) > 0) {
               $total = mysqli_num_rows($incoming_mail_count);
               //Get last notification item time
               $last_time = mysqli_query($conn, "SELECT notification_date FROM general_notifications WHERE notification_type = 'incoming_mail' AND notification_receiver = '$email' AND notification_status = 'Unseen' ORDER BY notificationID DESC LIMIT 1");
               $value = mysqli_fetch_assoc($last_time);
               $notification_date = $value['notification_date'];
               $counter = timeAgo($notification_date);
               echo "<a href='#' class='dropdown-item' style='font-size: 0.9rem;'>
                        <i class='fas fa-envelope mr-2'></i>
                         $total New mails
                        <span class='float-right text-muted text-sm'>$counter</span>
                     </a>
                      <div class='dropdown-divider'></div>
                     ";
            }
         ?>  
         <!-- Check user registrations  -->
         <?php 
             $affiliate_registration_count = mysqli_query($conn, "SELECT * FROM general_notifications WHERE notification_type = 'user_registration' AND notification_receiver = '$email' AND notification_status = 'Unseen'");
            if (mysqli_num_rows($affiliate_registration_count) > 0) {
               $total = mysqli_num_rows($affiliate_registration_count);
               //Get last notification item time
               $last_time = mysqli_query($conn, "SELECT notification_date FROM general_notifications WHERE notification_type = 'user_registration' AND notification_receiver = '$email' AND notification_status = 'Unseen' ORDER BY notificationID DESC LIMIT 1");
               $value = mysqli_fetch_assoc($last_time);
               $notification_date = $value['notification_date'];
               $counter = timeAgo($notification_date);
               echo "<a href='#' class='dropdown-item' style='font-size: 0.9rem;'>
                        <i class='fas fa-users mr-2'></i>
                         $total User signups
                        <span class='float-right text-muted text-sm'>$counter</span>
                     </a>
                      <div class='dropdown-divider'></div>
                     ";
            }
         ?>  
         <!-- Check worker registrations  -->
          <?php 
            $vendor_registration_count = mysqli_query($conn, "SELECT * FROM general_notifications WHERE notification_type = 'worker_application' AND notification_receiver = '$email' AND notification_status = 'Unseen'");
            if (mysqli_num_rows($vendor_registration_count) > 0) {
               $total = mysqli_num_rows($vendor_registration_count);
               //Get last notification item time
               $last_time = mysqli_query($conn, "SELECT notification_date FROM general_notifications WHERE notification_type = 'worker_application' AND notification_receiver = '$email' AND notification_status = 'Unseen' ORDER BY notificationID DESC LIMIT 1");
               $value = mysqli_fetch_assoc($last_time);
               $notification_date = $value['notification_date'];
               $counter = timeAgo($notification_date);
               echo "<a href='#' class='dropdown-item' style='font-size: 0.9rem;'>
                        <i class='fas fa-user-shield mr-2'></i>
                         $total Worker signups
                        <span class='float-right text-muted text-sm'>$counter</span>
                     </a>
                      <div class='dropdown-divider'></div>
                     ";
            }
         ?>  
        <!-- Check email subscriptions  -->
         <?php 
            $email_subscription_count = mysqli_query($conn, "SELECT * FROM general_notifications WHERE notification_type = 'email_subscription' AND notification_receiver = '$email' AND notification_status = 'Unseen'");
            if (mysqli_num_rows($email_subscription_count) > 0) {
               $total = mysqli_num_rows($email_subscription_count);
               //Get last notification item time
               $last_time = mysqli_query($conn, "SELECT notification_date FROM general_notifications WHERE notification_type = 'email_subscription' AND notification_receiver = '$email' AND notification_status = 'Unseen' ORDER BY notificationID DESC LIMIT 1");
               $value = mysqli_fetch_assoc($last_time);
               $notification_date = $value['notification_date'];
               $counter = timeAgo($notification_date);
               echo "<a href='#' class='dropdown-item' style='font-size: 0.9rem;'>
                        <i class='fas fa-plus mr-2'></i>
                         $total Email subscriptions
                        <span class='float-right text-muted text-sm'>$counter</span>
                     </a>
                      <div class='dropdown-divider'></div>
                     ";
            }
         ?>   
         <div class="dropdown-divider"></div>
           <a href="../views/timeline.php" class="dropdown-item dropdown-footer">View all</a>
         </div>
      </li>
    </ul>
  </nav>