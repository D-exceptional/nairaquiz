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
    
    $walletDetails = getWalletDetails($conn, $userID);
    
    if ($walletDetails) {
        $account_number = $walletDetails['account_number'];
        $bank = $walletDetails['bank'];
        $bank_code = $walletDetails['bank_code'];
        $recipient_code = $walletDetails['recipient_code'];
    } else {
        // Handle missing wallet
        echo "Wallet not found for this user.";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User | Payout History</title>
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
            <h1 id='payCounter'><b>Payout History</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Payout History</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content" style='overflow-y: auto !important;box-sizing: border-box;'>

      <!-- Default box -->
      <div class="card" style='height: 550px;'>
        <div class="card-header">
          <h3 class="card-title">History Details</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
          </div>
        </div>
        <div class="card-body p-0"  style='overflow-x: auto !important;white-space: normal;'>
          <table class="table table-striped projects">
            <thead>
                <tr>
                    <th>Fullname</th>
                    <th>Email</th>
                    <th>Amount</th>
                    <th>Account</th>
                    <th>Bank</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php
                // Prepare the SQL query
                $stmt = $conn->prepare("SELECT payment_amount, payment_status FROM withdrawals WHERE payment_email = ? ORDER BY payment_date DESC");
                $stmt->bind_param("s", $email);
                
                if ($stmt->execute()) {
                    $result = $stmt->get_result();
                
                    if ($result->num_rows > 0) {
                        while ($value = $result->fetch_assoc()) {
                            $amount = $value['payment_amount'];
                            $payout_amount = number_format($amount, 2, '.', ',');
                            $status = $value['payment_status'] === 'Pending' ? "<button class='btn btn-danger btn-sm'>Pending</button>" : "<button class='btn btn-success btn-sm'>Completed</button>";
                
                            echo "
                                <tr>
                                    <td>$fullname</td>
                                    <td>$email</td>
                                    <td>$payout_amount</td>
                                    <td>$account_number</td>
                                    <td>$bank</td>
                                    <td>$status</td>
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
  <!-- /.content-wrapper 

  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.1.0
    </div>
    <strong>Copyright &copy; <a href="#!">chromstack</a>.</strong> All rights reserved.
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
</body>
</html>