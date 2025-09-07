<?php 
  require '../server/conn.php';
  require '../session.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User | Timeline</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../admin/plugins/fontawesome-free/css/all.min.css">
  <!-- AdminLTE css -->
  <link rel="stylesheet" href="../../admin/dist/css/adminlte.min.css">
  <!-- Include SweetAlert CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <!-- Sweetalert JS Script -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="<?php echo getCacheBustedUrl('../../admin/dist/css/overlay.css'); ?>">
</head>
<body class="hold-transition sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

<!-- Navbar -->

<?php include '../navbar.php'; ?>

<!-- /.navbar -->

<!-- Main Sidebar Container -->

<?php include '../sidenav.php'; ?>

<!-- / Main Sidebar Container -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><b>Timeline</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Timeline</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content" style='overflow-y: auto !important;box-sizing: border-box;'>
      <div class="container-fluid">

        <!-- Timelime example  -->
        <div class="row" style='height: 550px;'>
          <div class="col-md-12">
            <!-- The time line -->
            <div class="timeline">

                <?php 

                   //echo date('Y-m-d H:i:s');

                    //Function definition

                    function calcTimeAgo($time_ago)  //The argument $time_ago is in timestamp (Y-m-d H:i:s) format.
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

                  $notification_query = mysqli_query($conn, "SELECT * FROM general_notifications WHERE notification_receiver = '$email' GROUP BY notification_details ORDER BY notification_date DESC");
                  if (mysqli_num_rows($notification_query) > 0) {
                      while ($val = mysqli_fetch_assoc($notification_query)) {
                            $notification_title = $val['notification_title'];
                            $notification_details = $val['notification_details'];
                            $notification_type = $val['notification_type'];
                            $notification_date = $val['notification_date'];
                            $formatted_notification_date = substr($val['notification_date'], 0, -8);
                            $timestamp = calcTimeAgo($notification_date);                       

                            echo "<!-- timeline time label --
                                    <div class='time-label'>
                                      <span class='bg-red'>$formatted_notification_date</span>
                                    </div>
                                    -- End timeline time label -->
                                  ";

                            switch ($notification_type) {
                              case 'incoming_mail':
                                echo "
                                      <!-- timeline item -->
                                      <div>
                                        <i class='fas fa-envelope bg-blue'></i>
                                        <div class='timeline-item'>
                                          <span class='time'><i class='fas fa-clock'></i>  $timestamp</span>
                                          <h3 class='timeline-header'>Incoming mail</h3>
                                          <div class='timeline-body'>$notification_details</div>
                                          <div class='timeline-footer'>
                                            <a class='btn btn-primary btn-sm'>Read more</a>
                                          </div>
                                        </div>
                                      </div>
                                      <!-- END timeline item -->
                                    ";
                              break;
                              case 'wallet_fund':
                                echo "
                                        <!-- timeline item -->
                                        <div>
                                          <i class='fas fa-wallet bg-yellow'></i>
                                          <div class='timeline-item'>
                                            <span class='time'><i class='fas fa-clock'></i>  $timestamp</span>
                                            <h3 class='timeline-header'>Wallet fund</h3>
                                            <div class='timeline-body'>$notification_details</div>
                                          </div>
                                        </div>
                                        <!-- END timeline item -->
                                      ";
                              break;
                              case 'game_payout':
                                echo "
                                        <!-- timeline item -->
                                        <div>
                                          <i class='fas fa-university bg-blue'></i>
                                          <div class='timeline-item'>
                                            <span class='time'><i class='fas fa-clock'></i>  $timestamp</span>
                                            <h3 class='timeline-header'>Game payout</h3>
                                            <div class='timeline-body'>$notification_details</div>
                                          </div>
                                        </div>
                                        <!-- END timeline item -->
                                      ";
                              break;
                            }
                      }
                  }
                ?>
                
            </div>
          </div>
          <!-- /.col -->
        </div>
      </div>
      <!-- /.timeline -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper --

  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.1.0
    </div>
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>-->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- Zoom Start --->
<div id="zoomBox">
    <i class="fas fa-expand"></i>
</div>
<!-- Zoom End --->

<!-- Loading Start -->
<div id="timer-overlay">
    
    <!-- Background Video inside the overlay -->
    <video class="bg-video" autoplay muted loop playsinline>
        <source src="../../assets/docs/bg-video-2.mp4" type="video/mp4" />
        Your browser does not support the video tag.
    </video>
    
    <!-- Dim overlay on top of video -->
    <div class="video-dimmer"></div>

    <!-- Loading Elements -->
    <div class="shine"></div>
    <i class="fas fa-spinner loader"></i>
    <span class="loading-text"></span>
    
    <!-- Action Button -->
    <button>Join Session <i class="fas fa-arrow-right"></i></button>
</div>
<!-- Loading End -->

<!-- jQuery -->
<script src="../../admin/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../../admin/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../admin/dist/js/demo.js"></script>
<script src="../scripts/events.js" type='module'></script>
<script src="<?php echo getCacheBustedUrl('../scripts/notifications.js'); ?>" type="module"></script>
</body>
</html>
