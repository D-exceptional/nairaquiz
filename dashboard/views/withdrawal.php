<?php 
  require '../server/conn.php';
  require '../session.php';
  
  function getWalletDetails(mysqli $conn, int $userID): ?array {
    $stmt = $conn->prepare("SELECT account_number, bank, bank_code, recipient_code FROM wallet WHERE userID = ?");
    $stmt->bind_param("i", $userID);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $wallet = $result->fetch_assoc();
            $stmt->close();
            return [
                'account_number' => $wallet['account_number'],
                'bank' => $wallet['bank'],
                'bank_code' => $wallet['bank_code'],
                'recipient_code' => $wallet['recipient_code']
            ];
        }
    }

    $stmt->close();
    return null; // No wallet found or error occurred
  }
  
  function getWalletAmount(mysqli $conn, int $userID): ?array {
    $stmt = $conn->prepare("SELECT wallet_amount FROM wallet_savings WHERE userID = ?");
    $stmt->bind_param("i", $userID);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $wallet = $result->fetch_assoc();
            $stmt->close();
            return [
                'amount' => $wallet['wallet_amount'],
            ];
        }
    }

    $stmt->close();
    return null; // No wallet found or error occurred
  }
    
    $walletDetails = getWalletDetails($conn, $userID);
    if ($walletDetails) {
        $account_number = $walletDetails['account_number'];
        $bank = $walletDetails['bank'];
        $bank_code = $walletDetails['bank_code'];
        $recipient_code = $walletDetails['recipient_code'];
    } 
    
    $amountDetails = getWalletAmount($conn, $userID);
    $walletAmount = $amountDetails['amount'];
?> 

<!DOCTYPE html>
<html lang="en" style='overflow-x: hidden !important;width: 100vw;height: 100vh;'>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User | Withdrawal</title>
  <link type="image/x-icon" rel="icon" href="/assets/img/short-logo.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../admin/plugins/fontawesome-free/css/all.min.css">
  <!-- Include SweetAlert CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <!-- Sweetalert JS Script -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Theme style -->
  <link rel="stylesheet" href="../../admin/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?php echo getCacheBustedUrl('../../admin/dist/css/overlay.css'); ?>">
  <link rel="stylesheet" href="<?php echo getCacheBustedUrl('../../assets/css/popup.css'); ?>">
  <style>
       html, body{
        overflow: hidden;
      }
  </style>
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
            <h1 style='color: #212529'><b>Withdrawal</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Withdrawal</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content" style='overflow-y: auto !important;box-sizing: border-box;'>
      <!-- Default box -->
      <div class="card" style="height: 550px;">
        <div class="card-header">
          <h3 class="card-title">Withdrawal</h3>
          <div class="card-tools">
            <?php
              // Get the current day of the week
             // $currentDay = date('l'); // 'l' (lowercase 'L') returns the full textual representation of the day
              //$currentHour = date('H');
              // Check if the current day is Thursday
              //if ($currentDay === 'Thursday' && $currentHour < 23) { //Withdrawal should be done before 11pm
                //Check if this person has placed withdrawal
                //$check = mysqli_query($conn, "SELECT * FROM withdrawals WHERE withdrawal_email = '$email'");
                if($walletAmount >= 5000){
                  echo "<button class='btn btn-danger btn-sm' id='withdraw'>Place Withdrawal</button> ";
                }
              //} 
            ?>
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="card-body p-0" style='overflow-x: auto !important;white-space: normal;'>
          <table class="table table-striped projects">
              <thead>
                <tr>
                  <th>SN</th>
                  <th>Amount</th>
                  <th>Account</th>
                  <th>Bank</th>
                  <th>Reference</th>
                  <th>Narration</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                    // Prepare the statement
                    $stmt = $conn->prepare("SELECT payment_amount, payment_account, payment_bank, payment_txref, payment_narration, payment_date, payment_status FROM withdrawals WHERE userID = ? ORDER BY paymentID DESC");
                    $stmt->bind_param("i", $userID);
                    
                    if ($stmt->execute()) {
                        $result = $stmt->get_result();
                    
                        if ($result->num_rows > 0) {
                            while ($value = $result->fetch_assoc()) {
                                $amount = $value['payment_amount'];
                                $account = htmlspecialchars($value['payment_account']);
                                $bank = htmlspecialchars($value['payment_bank']);
                                $reference = htmlspecialchars($value['payment_txref']);
                                $narration = htmlspecialchars($value['payment_narration']);
                                $date = htmlspecialchars($value['payment_date']);
                    
                                // Format amount
                                $payout_amount = '₦' . number_format($amount, 2, '.', ',');
                    
                                // Format status button
                                $status = $value['payment_status'] === 'Pending' ? "<button class='btn btn-danger btn-sm'>Pending</button>" : "<button class='btn btn-success btn-sm'>Completed</button>";
                    
                                // Action button
                                $action = "<button class='btn btn-info btn-sm'>View</button>";
                    
                                // Output table row
                                echo "
                                    <tr class='rows'>
                                        <td>#</td>
                                        <td>$payout_amount</td>
                                        <td>$account</td>
                                        <td>$bank</td>
                                        <td>$reference</td>
                                        <td>$narration</td>
                                        <td>$date</td>
                                        <td>$status</td>
                                        <td>$action</td>
                                    </tr>
                                ";
                            }
                        }
                    }
                    
                    $stmt->close();
                    ?>
              </tbody>
          </table>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

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

<div id='withdraw-overlay'>
  <form class="form-horizontal" id='withdraw-form'>
      <center>
          <h3>Withdraw Funds</h3>
      </center>
    <div class="form-group">
        <div class='form-text'>
            <label for="facebook" class="col-sm-2 col-form-label">Available Amount (₦)</label>
        </div>
      <div class="col-sm-10">
        <input type="number" class="form-control" id='availableAmount' name="available_amount" value="<?php echo $walletAmount; ?>" disabled>
        <p id='accountNumber' style='display: none;'><?php echo $account_number; ?></p>
        <p id='bankName' style='display: none;'><?php echo $bank; ?></p>
      </div>
    </div>
    <div class="form-group">
        <div class='form-text'>
            <label for="facebook" class="col-sm-2 col-form-label">Withdrawal Amount (₦)</label>
        </div>
      <div class="col-sm-10">
        <input type="number" class="form-control" id='withdrawalAmount' name="withdrawal_amount" placeholder="Amount to withdraw">
      </div>
    </div>
    <div class="form-group">
      <div class="offset-sm-2 col-sm-10">
          <center>
            <?php
                $button = $walletAmount === 0 ? "<button type='button' class='btn btn-danger' disabled>Insufficient Balance</button>" : "<button type='submit' class='btn btn-success' id='request-withdrawal'>Place Withdrawal</button>";
                echo $button;
            ?>
        </center>
      </div>
    </div>
    <center>
        <span id='info-span'></span>
    </center>
  </form>
  <div id='close-view'><i class="fas fa-times"></i></div>
</div>

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
<!-- Core Script -->
<script src="../scripts/events.js" type='module'></script>
<!--<script src="../../assets/scripts/tawkto.js"></script>-->
<script src="<?php echo getCacheBustedUrl('../scripts/notifications.js'); ?>" type="module"></script>
<script src="<?php echo getCacheBustedUrl('../scripts/withdrawal.js'); ?>"></script>
</body>
</html>
