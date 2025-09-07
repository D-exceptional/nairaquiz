<?php
require '../server/conn.php';
require '../session.php';

/**
 * Fetches wallet amount for a specific user securely using prepared statement.
 *
 * @param mysqli $conn   The MySQLi connection object
 * @param int|string $userID The user ID
 * @return float Returns the wallet amount or 0 if not found
 */
function getWalletAmount($conn, $userID) {
    $wallet_amount = 0;

    $stmt = $conn->prepare("SELECT wallet_amount FROM wallet WHERE userID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $stmt->bind_result($wallet_amount_result);
        if ($stmt->fetch()) {
            $wallet_amount = $wallet_amount_result;
        }
        $stmt->close();
    }

    return $wallet_amount;
}

// Example usage
$wallet_amount = getWalletAmount($conn, $userID);
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
      <div class="row" style='height: 550px;'>
        <!-- Image Section -->
        <div class="col-md-6">
         <img src="../../assets/img/multiplayer.jpeg" alt="image" style="width: 100%;height: 100%;">
        </div>
        <div class="col-md-6">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Quiz Rules</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="form-group">
                <h3><b>The Rules</b></h3>
                <span>
                    There are rules guiding the conduct of quiz sessions on NairaQuiz.
                    As a player, you are expected to abide by the rules, to enable you stand a chance to perform excellently and win cool prizes.
                    Here are the laid down rules:
                </span>
                <br>
                <br>
                <ul style='text-align: left;'>
                    <li><b>Time Limit:</b> Each question has a 15-second time frame. Players must select an answer within this time.</li>
                    <li><b>Automatic Game Over:</b> Failure to answer a question within the allotted 15 seconds will result in immediate game termination.</li>
                    <li><b>Completion Requirement:</b> Players must answer all questions in their chosen format</li>
                    <li><b>Fair Play Policy:</b> Cheating of any kind is strictly prohibited. Detection of cheating will lead to instant termination of the game and forfeiture of any potential winnings.</li>
                </ul>
                <!--<br>-->
                <span>
                    All the best to you!
                </span>
              </div>
              <div class="row">
                <div class="col-12">
                    <?php 
                        if($wallet_amount < 1000){
                            echo "<input type='button' id='fund' value='Fund Wallet' class='btn btn-danger float-left'>";
                        }
                        else{
                            echo "<input type='button' id='start' value='Join Contest' class='btn btn-success float-left'>";
                        }
                    ?>
                    <input type="text" id="walletAmount" value="<?php echo $wallet_amount; ?>" hidden>
                </div>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper --

  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.1.0
    </div>
    <strong>Copyright &copy; 2014-2021 <a href="#!">chromstack</a>.</strong> All rights reserved.
  </footer>-->

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
<!-- Sweetalert JS Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Core Script -->
<script src="../scripts/events.js" type='module'></script>
<script src="<?php echo getCacheBustedUrl('../scripts/notifications.js'); ?>" type="module"></script>
<script src="<?php echo getCacheBustedUrl('../scripts/game.js'); ?>" type='module'></script>
</body>
</html>

