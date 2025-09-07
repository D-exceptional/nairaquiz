<?php 
    require '../server/conn.php';
    require 'session.php';
?>  

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Dashboard</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <!--<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">-->
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="../plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../plugins/summernote/summernote-bs4.min.css">
  <!-- Include SweetAlert CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <!-- Sweetalert JS Script -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    html, body{
      overflow: hidden;
    }
    
    /* width 
      ::-webkit-scrollbar {
        width: 10px;
      }
    */
      
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
    
    /* Extra large devices (large laptops and desktops, 1200px and up) 
    @media only screen and (min-width: 1200px) {
      .col-lg-3 .col-6 {
        width: 25% !important;
        max-width: 25% !important;
      }
    }*/
    
    .inner{
      background-image: url(../../assets/img/dashboard-bg.jpeg);
      background-position: center;
      background-size: cover;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Preloader --
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="../dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div>
  -->

  <!-- Navbar -->
  <?php include 'home-notification.php'; ?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #181d38 !important;">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
       <img src="../../assets/img/nairaQuiz.jpg" alt="Logo" class="brand-image img-circle elevation-3" style="width: 40px;height: 40px;border-radius: 50%;opacity: .9;">
      <span class="brand-text font-weight-light"><b>NairaQuiz</b></span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
        <?php
            $image = $profile === 'null' ? 
            "<img src='../../assets/img/user.png' class='img-circle elevation-2' style='width: 50px !important;height: 50px !important;' alt='User Image'>"
            : "<img src='../../uploads/$profile' class='img-circle elevation-2' style='width: 50px !important;height: 50px !important;' alt='User Image'>";
            echo $image;
          ?>
        </div>
        <div class="info">
        <?php
            echo "<a href='./views/profile.php' class='d-block' style='overflow: hidden !important;white-space: nowrap !important;text-overflow: ellipsis !important;'>$fullname</a>";
          ?>
        </div>
      </div>
      <!-- Sidebar Menu -->
      <nav class="mt-2" style="overfolw-x: hidden;overflow-y: visible !imporatnt;">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-home"></i>
                <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-hourglass-end"></i>
              <p>
                Approvals
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./views/game-approval.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Gaming</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./views/investment-approval.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Investment</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-chart-line"></i>
              <p>
                Pay-ins
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./views/game-payin.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Gaming</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./views/investment-payin.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Investment</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-chart-line"></i>
              <p>
                Pay-outs
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./views/game-payout.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Gaming</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./views/investment-payout.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Investment</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-envelope-open"></i>
              <p>
                Mail
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./views/mailbox.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Inbox</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./views/compose-mail.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Compose</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="./views/questions.php" class="nav-link">
              <i class="nav-icon fas fa-layer-group"></i>
              <p>
                Questions
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./views/multiplayer.php" class="nav-link">
              <i class="nav-icon fas fa-retweet"></i>
              <p>
                Multiplayer
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./views/trials.php" class="nav-link">
              <i class="nav-icon fas fa-medal"></i>
              <p>
                Trials
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./views/users.php" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Users
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./views/investors.php" class="nav-link">
              <i class="nav-icon fas fa-user-cog"></i>
              <p>
                Investors
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./views/workers.php" class="nav-link">
              <i class="nav-icon fas fa-user-tag"></i>
              <p>
                Workers
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./views/ambassadors.php" class="nav-link">
              <i class="nav-icon fas fa-user-graduate"></i>
              <p>
                Ambassadors
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./views/profile.php" class="nav-link">
              <i class="fas fa-cog nav-icon"></i>
              <p>Settings</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./server/logout.php" class="nav-link">
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
  <!-- /.content-wrapper --
  <footer class="main-footer">
    <strong>Copyright &copy; 2023 <a href="#!">Chromstack</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 1.1.0
    </div>
  </footer>-->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark" style="background-color: #181d38 !important;">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="../plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="../plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="../plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="../plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="../plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="../plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="../plugins/moment/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="../plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- Admin main functional files -->
<script src="<?php echo getCacheBustedUrl('./scripts/home-notification.js'); ?>" type="module"></script>
<script src="<?php echo getCacheBustedUrl('./scripts/overview.js'); ?>" type='module'></script>

</body>
</html>
