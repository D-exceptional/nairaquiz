<?php 
require './server/conn.php';
require 'session.php';
?>  

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User | Dashboard</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../admin/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <!--<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">-->
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="../admin/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="../admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="../admin/plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../admin/dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../admin/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="../admin/plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../admin/plugins/summernote/summernote-bs4.min.css">
  <!-- Include SweetAlert CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="<?php echo getCacheBustedUrl('../admin/dist/css/overlay.css'); ?>">
  <style>
    html, body{
      overflow: hidden;
    }
      
    /* Extra small devices (phones, 600px and down) */
    @media screen and (max-width: 600px) {
        .col-lg-3.col-6
        {
            min-width: 100% !important;
        }
    }
    
    /* Small devices (portrait tablets and large phones, 600px and up) */
    @media screen and (min-width: 600px) and (max-width: 992px) {
        .col-lg-3.col-6
        {
            width: 50% !important;
        }
    }
    
    /* Medium devices (landscape tablets, 768px and up) */
    @media screen and (min-width: 768px) {
        .col-lg-3.col-6
        {
            width: 50% !important;
        }
    }
    
    /* Large devices (laptops/desktops, 992px and up) */
    @media screen and (min-width: 992px) {
        .col-lg-3.col-6
        {
            width: 25% !important;
        }
    }
        
    .inner{
        background-image: url(../assets/img/dashboard-bg.jpeg);
        background-position: center;
        background-size: cover;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Preloader --
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="../admin/dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div>-->

  <!-- Navbar -->

   <?php include 'home-notification.php'; ?>

  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #181d38 !important;">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="../assets/img/nairaQuiz.jpg" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light"><b>NairaQuiz</b></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
        <?php
            $image = $profile === 'null' ? "<img src='../assets/img/user.png' class='img-circle elevation-2' width='50px' height='50px' alt='User Image'>" : "<img src='../uploads/$profile' class='img-circle elevation-2' style='width: 50px !important;height: 50px !important;' alt='User Image'>";
            echo $image;
          ?>
        </div>
        <div class="info">
          <?php
            echo "<a href='#' class='d-block' style='overflow: hidden !important;white-space: nowrap !important;text-overflow: ellipsis !important;'>$fullname</a>";
          ?>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-home"></i>
              <p>Dashboard</p>
            </a>
          </li>
           <li class="nav-item">
            <a href="views/wallet.php" class="nav-link">
              <i class="nav-icon fas fa-wallet"></i>
              <p>Wallet</p>
            </a>
          </li>
          <li class='nav-item'>
            <a href='views/game.php' class='nav-link'>
              <i class='nav-icon fas fa-users'></i>
              <p>Multiplayer</p>
            </a>
          </li>
          <?php 
              if(in_array($userID, [3, 7])){
                  echo 
                  '<li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="nav-icon fas fa-medal"></i>
                      <p>
                        Challenge
                        <i class="fas fa-angle-left right"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <a href="views/quiz.php?type=5" class="nav-link">
                          <i class="far fa-circle nav-icon"></i>
                          <p>5 Questions</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="views/quiz.php?type=7" class="nav-link">
                          <i class="far fa-circle nav-icon"></i>
                          <p>7 Questions</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="views/quiz.php?type=10" class="nav-link">
                          <i class="far fa-circle nav-icon"></i>
                          <p>10 Questions</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="views/quiz.php?type=14" class="nav-link">
                          <i class="far fa-circle nav-icon"></i>
                          <p>14 Questions</p>
                        </a>
                      </li>
                    </ul>
                </li>
              ';
              }
          ?>
          <li class="nav-item">
            <a href="views/trials.php" class="nav-link">
              <i class="fas fa-retweet nav-icon"></i>
              <p>Trials</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="views/withdrawal.php" class="nav-link">
              <i class="fas fa-download nav-icon"></i>
              <p>Withdrawal</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-hand-holding-usd"></i>
              <p>
                Payouts
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="views/pending-payout.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Pending</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="views/payout-history.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>History</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-globe"></i>
              <p>
                Community
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="https://www.facebook.com/profile.php?id=61567789027719" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Facebook</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://instagram.com/nairaquiz" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Instagram</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://whatsapp.com/channel/0029VbAiVSKKAwEfwLILhT2J" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>WhatsApp</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://x.com/nairaquiz24965?t=58F-AImMCqisk_xQrpKpDg&s=09" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Twitter</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://www.tiktok.com/@www.nairaquiz.com?_t=ZS-8yUdJ1TqY9C&_r=1" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Tiktok</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://youtube.com/@nairaquiz?si=IJw1InTdVhp-5a3H" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>YouTube</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="views/settings.php" class="nav-link">
              <i class="fas fa-cog nav-icon"></i>
              <p>Settings</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="server/logout.php" class="nav-link">
              <i class="nav-icon fas fa-arrow-left"></i>
              <p>Logout</p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><b>Dashboard</b></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <!--<li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard v1</li>-->
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content" style="overflow-y: auto !important;box-sizing: border-box;">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row" id="content-overview" style="height: 580px;"></div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark" style="background-color: #181d38 !important;">
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
        <source src="../assets/docs/bg-video-2.mp4" type="video/mp4" />
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
<script src="../admin/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="../admin/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="../admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="../admin/plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="../admin/plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="../admin/plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="../admin/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="../admin/plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="../admin/plugins/moment/moment.min.js"></script>
<script src="../admin/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="../admin/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="../admin/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="../admin/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="../admin/dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../admin/dist/js/demo.js"></script>
<!-- Sweetalert JS Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Admin main functional files -->
<script src="./scripts/events.js" type='module'></script>
<script src="<?php echo getCacheBustedUrl('./scripts/home-notification.js'); ?>" type="module"></script>
<script src="<?php echo getCacheBustedUrl('./scripts/overview.js'); ?>" type='module'></script>

</body>
</html>