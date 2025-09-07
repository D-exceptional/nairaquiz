<?php
require '../server/conn.php';
require '../session.php';

/**
 * Fetch the wallet amount for a user.
 *
 * @param mysqli $conn      The MySQLi connection object
 * @param int    $userID    The user ID
 * @return float            Wallet amount or 0 on failure
 */
function getWalletAmount(mysqli $conn, int $userID): float {
    $stmt = $conn->prepare("SELECT wallet_amount FROM wallet WHERE userID = ?");
    if (!$stmt) return 0;

    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($walletAmount);

    $wallet = 0;
    if ($stmt->fetch()) {
        $wallet = $walletAmount;
    }

    $stmt->close();
    return $wallet;
}

// Ensure user ID is valid
if (!isset($userID) || !is_numeric($userID)) {
    exit('Invalid user session.');
}

$walletAmount = getWalletAmount($conn, (int)$userID);

// Redirect if sufficient funds, else show message
if ($walletAmount < 500) { // Formerly 1000 (1K)
    // Redirect immediately to wallet page with a message (GET param optional)
    header("Location: ../views/game.php?msg=insufficient_funds");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User | Multiplayer</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../admin/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../admin/dist/css/adminlte.min.css">
  <!-- Include SweetAlert CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
            <h1><b>Quiz Section</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Quiz Section</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content" style='overflow-y: auto !important;box-sizing: border-box;'>
      <div class="row" style='height: 550px;background: black;'></div>
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

 <!-- Overlay Start -->
<div id='quiz-overlay'>
    <div id='quiz-inner'>
        <div id="quiz-header">
            <div id='counter'>
                <div id="progressContainer">
                    <div id="progressBar"></div>
                </div>
            </div>
            <div id='timer'>Timer: <b></b> secs</div>
            <div id='total'>
                <i class="fa fa-users" aria-hidden="true"></i>
                <b></b>
            </div>
        </div>
        <div id='overlay-content'>
            <h3>Question <b></b></h3>
            <br>
            <div id="question-text" class='no-select'>
            <div class="item-question"></div>
            </div>
            <br>
            <div id="answers"></div>
        </div>
        <div class="bubbles">
            <span></span><span></span><span></span><span></span><span></span>
            <span></span><span></span><span></span><span></span><span></span>
        </div>
        <!--
        <div id='close-overlay'>
            <i class="fa fa-times" aria-hidden="true"></i>
        </div>
        -->
    </div>
    <!-- Background Video inside the overlay -->
    <video autoplay muted loop playsinline>
        <source src="../../assets/docs/bgvideo.mp4" type="video/mp4" />
        Your browser does not support the video tag.
    </video>
    
    <!-- Dim overlay on top of video -->
    <!--<div class="videobg-dimmer"></div>-->
</div>
<!-- Overlay End -->

<!-- Notification Start --->
<div id='notification-overlay'>
  <div id='notification-div'>
    <!--<div id='close-notification'>X</div>-->
    <div id='icon-div'></div>
    <div id='message-div'></div>
  </div>
</div>
<!-- Notification End --->

<!-- Loading Start -->
<div id="loading-overlay">
    
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
</div>
<!-- Loading End -->

<!-- Text Start --->
<div id="text-pane"></div>
<!-- Text End --->

<!-- Scroll Start --->
<div id="navScroll">
    <i class="fas fa-arrow-down"></i>
</div>
<!-- Scroll End --->

<!-- jQuery -->
<script src="../../admin/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../../admin/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../admin/dist/js/demo.js"></script>
<!-- Sweetalert JS Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Core Script -->
<script src="../scripts/events.js" type='module'></script>
<script src="<?php echo getCacheBustedUrl('../scripts/notifications.js'); ?>" type="module"></script>
<script src="<?php echo getCacheBustedUrl('../scripts/multiplayer.js'); ?>" type="module"></script>
</body>
</html>

