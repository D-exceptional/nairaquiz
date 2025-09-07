<?php 
  require 'server/conn.php';
  require 'session.php';

  // Get bank details
  function getBankDetails(mysqli $conn, int $investorID): ?array {
    $stmt = $conn->prepare("SELECT account_number, bank_name FROM investor_finance WHERE investorID = ?");
    $stmt->bind_param("i", $investorID);

    if ($stmt->execute()) {
      $result = $stmt->get_result();
      if ($result->num_rows > 0) {
        $wallet = $result->fetch_assoc();
        $stmt->close();
        return [
          'account_number' => $wallet['account_number'],
          'bank' => $wallet['bank_name'],
        ];
      }
    }

    $stmt->close();
    return null; // No wallet found or error occurred
  }

  // Get wallet amount
  function getWalletAmount($table, $id, $type, $conn) {
    $stmt = $conn->prepare("SELECT wallet_amount FROM $table WHERE investorID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $type === 'Processed' ? '₦' . number_format($row['wallet_amount'], 2, '.', ',') : $row['wallet_amount'];
  }
  
  $walletDetails = getBankDetails($conn, $investorID);
  if ($walletDetails) {
    $account_number = $walletDetails['account_number'];
    $bank = $walletDetails['bank'];
  } 
?> 

<!DOCTYPE html>
<html lang="en" style='overflow-x: hidden !important;width: 100vw;height: 100vh;'>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Investor | Wallets</title>
  <link type="image/x-icon" rel="icon" href="/assets/img/short-logo.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../admin/plugins/fontawesome-free/css/all.min.css">
  <!-- Include SweetAlert CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <!-- Sweetalert JS Script -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Theme style -->
  <link rel="stylesheet" href="../admin/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?php echo getCacheBustedUrl('../admin/dist/css/overlay.css'); ?>">
  <link rel="stylesheet" href="<?php echo getCacheBustedUrl('../assets/css/popup.css'); ?>">
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
  <?php include 'navbar.php'; ?>
  <!-- /.navbar -->
 
  <!-- Main Sidebar Container -->
  <?php include 'sidenav.php'; ?>
  <!-- / Main Sidebar Container -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 style='color: #212529'><b>Wallets</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Home</a></li>
              <li class="breadcrumb-item active">Wallets</li>
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
          <h3 class="card-title">Wallets</h3>
          <div class="card-tools">
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
                  <th>Name</th>
                  <th>Total</th>
                  <th>Balance</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  // Initialize status
                  $status = "<button class='btn btn-success btn-sm'>Active</button>";

                  // Process Tier One wallet
                  $walletOne = 'Tier One';
                  $processedTotalOne = getWalletAmount('wallet_tier_one_backup', $investorID, 'Processed', $conn);
                  $processedBalanceOne = getWalletAmount('wallet_tier_one', $investorID, 'Processed', $conn);
                  $rawBalanceOne = getWalletAmount('wallet_tier_one', $investorID, 'Raw', $conn);
                  $actionOne = $rawBalanceOne <= 0 ? "<button class='btn btn-danger btn-sm' style='width: 150px;'>Insufficient Balance</button>" : "<button class='btn btn-success btn-sm' data-amount='$rawBalanceOne' id='withdrawalOne' style='width: 150px;'>Withdraw Funds</button>";

                  // Process Tier Two wallet
                  $walletTwo = 'Tier Two';
                  $processedTotalTwo = getWalletAmount('wallet_tier_two_backup', $investorID, 'Processed', $conn);
                  $processedBalanceTwo = getWalletAmount('wallet_tier_two', $investorID, 'Processed', $conn);
                  $rawBalanceTwo = getWalletAmount('wallet_tier_two', $investorID, 'Raw', $conn);
                  $actionTwo = $rawBalanceTwo <= 0 ? "<button class='btn btn-danger btn-sm' style='width: 150px;'>Insufficient Balance</button>" : "<button class='btn btn-success btn-sm' data-amount='$rawBalanceTwo' id='withdrawalTwo' style='width: 150px;'>Withdraw Funds</button>";

                  // Process Tier Three wallet
                  $walletThree = 'Tier Three';
                  $processedTotalThree = getWalletAmount('wallet_tier_three_backup', $investorID, 'Processed', $conn);
                  $processedBalanceThree = getWalletAmount('wallet_tier_three', $investorID, 'Processed', $conn);
                  $rawBalanceThree = getWalletAmount('wallet_tier_three', $investorID, 'Raw', $conn);
                  $actionThree = $rawBalanceThree <= 0 ? "<button class='btn btn-danger btn-sm' style='width: 150px;'>Insufficient Balance</button>" : "<button class='btn btn-success btn-sm' data-amount='$rawBalanceThree' id='withdrawalThree' style='width: 150px;'>Withdraw Funds</button>";
      
                  // Display Wallet One
                  echo "
                    <tr class='rows'>
                      <td>#</td>
                      <td>$walletOne</td>
                      <td>$processedTotalOne</td>
                      <td>$processedBalanceOne</td>
                      <td>$status</td>
                      <td>$actionOne</td>
                    </tr>
                    <tr class='rows'>
                      <td>#</td>
                      <td>$walletTwo</td>
                      <td>$processedTotalTwo</td>
                      <td>$processedBalanceTwo</td>
                      <td>$status</td>
                      <td>$actionTwo</td>
                    </tr>
                    <tr class='rows'>
                      <td>#</td>
                      <td>$walletThree</td>
                      <td>$processedTotalThree</td>
                      <td>$processedBalanceThree</td>
                      <td>$status</td>
                      <td>$actionThree</td>
                    </tr>
                  ";
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
        <input type="number" class="form-control" id='availableAmount' name="available_amount" value="0" disabled>
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
            //$button = $walletAmount === 0 ? "<button type='button' class='btn btn-danger' disabled>Insufficient Balance</button>" : "<button type='submit' class='btn btn-success' id='request-withdrawal'>Place Withdrawal</button>";
            echo "<button type='submit' class='btn btn-success' id='request-withdrawal'>Place Withdrawal</button>";
          ?>
        </center>
      </div>
    </div>
    <center>
      <span id='info-span'></span>
    </center>
  </form>
  <div id='close-view'>X</div>
</div>

<!-- jQuery -->
<script src="../admin/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../admin/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../admin/dist/js/demo.js"></script>
<!-- Core Script -->
<script src="<?php echo getCacheBustedUrl('scripts/notifications.js'); ?>" type="module"></script>
<script src="<?php echo getCacheBustedUrl('scripts/withdrawal.js'); ?>" type="module"></script>
</body>
</html>
