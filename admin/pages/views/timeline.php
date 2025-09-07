<?php 
  require '../server/conn.php';
  require '../session.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Timeline</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- AdminLTE css -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <!-- Include SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Sweetalert JS Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

                  $notification_query = mysqli_query($conn, "SELECT * FROM general_notifications WHERE notification_receiver = '$email' GROUP BY notification_details ORDER BY notification_date DESC");
                  if (mysqli_num_rows($notification_query) > 0) {
                      while ($val = mysqli_fetch_assoc($notification_query)) {
                            $notification_title = $val['notification_title'];
                            $notification_details = $val['notification_details'];
                            $notification_type = $val['notification_type'];
                            $notification_date = $val['notification_date'];
                            $formatted_notification_date = substr($val['notification_date'], 0, -8);
                            $timestamp = timeAgo($notification_date);                       

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
                                            <a class='btn btn-primary btn-sm' href='mailbox.php'>Read more</a>
                                          </div>
                                        </div>
                                      </div>
                                      <!-- END timeline item -->
                                    ";
                              break;
                              case 'user_registration':
                                echo "
                                        <!-- timeline item -->
                                        <div>
                                          <i class='fas fa-user bg-green'></i>
                                          <div class='timeline-item'>
                                            <span class='time'><i class='fas fa-clock'></i>  $timestamp</span>
                                            <h3 class='timeline-header'>User registration</h3>
                                            <div class='timeline-body'>$notification_details</div>
                                          </div>
                                        </div>
                                        <!-- END timeline item -->
                                      ";
                              break;
                              case 'worker_application':
                                echo "
                                        <!-- timeline item -->
                                        <div>
                                          <i class='fas fa-user-shield bg-yellow'></i>
                                          <div class='timeline-item'>
                                            <span class='time'><i class='fas fa-clock'></i>  $timestamp</span>
                                            <h3 class='timeline-header'>Worker registration</h3>
                                            <div class='timeline-body'>$notification_details</div>
                                          </div>
                                        </div>
                                        <!-- END timeline item -->
                                      ";
                              break;
                              case 'email_subscriptions':
                                echo "
                                        <!-- timeline item -->
                                        <div>
                                          <i class='fas fa-plus bg-purple'></i>
                                          <div class='timeline-item'>
                                            <span class='time'><i class='fas fa-clock'></i>  $timestamp</span>
                                            <h3 class='timeline-header'>Email subscription</h3>
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

<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>
<script src="../scripts/indent.js" type='module'></script>
<script src="<?php echo getCacheBustedUrl('../scripts/notifications.js'); ?>" type="module"></script>
</body>
</html>
