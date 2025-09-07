<?php 
  require '../server/conn.php';
  require '../session.php';
?> 
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Payouts</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <!-- Include SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .text-center{
        text-align: center;
    }
</style>
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
            <h1>
                <b>
                    <?php
                        // Initiate status
                        $statusPending = 'Pending';
                        
                        // 1. Get number of recipients (beneficiaries)
                        $count_stmt = $conn->prepare("SELECT COUNT(*) AS total_recipients FROM withdrawals WHERE payment_status = ?");
                        $count_stmt->bind_param("s", $statusPending);
                        $count_stmt->execute();
                        $count_result = $count_stmt->get_result();
                        $recipients = ($count_result->fetch_assoc()['total_recipients']) ?? 0;
                        $count_stmt->close();
                        
                        // 2. Get total payout amount
                        $sum_stmt = $conn->prepare("SELECT SUM(payment_amount) AS total_amount FROM withdrawals WHERE payment_status = ?");
                        $sum_stmt->bind_param("s", $statusPending);
                        $sum_stmt->execute();
                        $sum_result = $sum_stmt->get_result();
                        $total_amount = ($sum_result->fetch_assoc()['total_amount']) ?? 0;
                        $sum_stmt->close();
                        
                        // 3. Format and display
                        $formatted_amount = "&#x20A6;" . number_format($total_amount, 2, '.', ',');
                        echo "Payouts ($formatted_amount, $recipients Beneficiaries)";
                    ?>
                </b>
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Payouts</li>
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
          <h3 class="card-title">Payouts</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-success btn-sm" style='margin-left: 5px;' id='pay-all'>Pay All</button>
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
                    <th>Email</th>
                    <th>Amount</th>
                    <th>Account</th>
                    <th>Bank</th>
                    <th>Reference</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                    // Fetch all withdrawal records
                    $statusCheck = 'Pending';
                    $history_stmt = $conn->prepare("SELECT * FROM withdrawals WHERE payment_status = ? ORDER BY paymentID DESC");
                    $history_stmt->bind_param("s", $statusCheck);
                    $history_stmt->execute();
                    $history_result = $history_stmt->get_result();
                    
                    if ($history_result->num_rows > 0) {
                        // Prepare user info statement once
                        $user_stmt = $conn->prepare("SELECT fullname FROM users WHERE userID = ?");
                    
                        while ($value = $history_result->fetch_assoc()) {
                            $email = $value['payment_email'];
                            $amount = $value['payment_amount'];
                            $account = $value['payment_account'];
                            $bank = $value['payment_bank'];
                            $reference = $value['payment_txref'];
                            $date = $value['payment_date'];
                            $status = $value['payment_status'];
                            $userID = $value['userID'];
                    
                            // Format amount
                            $payout_amount = number_format($amount, 2, '.', ',');
                    
                            // Get user's fullname
                            $user_stmt->bind_param("i", $userID);
                            $user_stmt->execute();
                            $user_result = $user_stmt->get_result();
                            $user_data = $user_result->fetch_assoc();
                            $fullname = $user_data['fullname'] ?? 'User';
                    
                            // Format status and action buttons
                            if ($status === 'Pending') {
                                $state = "<button class='btn btn-danger btn-sm'>Pending</button>";
                                $button = "<button class='btn btn-info btn-sm'>Pay</button>";
                            } else {
                                $state = "<button class='btn btn-success btn-sm'>Completed</button>";
                                $button = "<button class='btn btn-info btn-sm'><i class='fas fa-check' style='padding-right: 5px;'></i> Done</button>";
                            }
                    
                            // Output table row
                            echo "
                                <tr class='rows'>
                                    <td>#</td>
                                    <td class='fullname'>$fullname</td>
                                    <td class='email'>$email</td>
                                    <td class='amount'>&#x20A6;$payout_amount</td>
                                    <td class='account'>$account</td>
                                    <td class='bank'>$bank</td>
                                    <td class='reference'>$reference</td>
                                    <td>$date</td>
                                    <td class='status'>$state</td>
                                    <td class='action'>$button</td>
                                </tr>
                            ";
                        }
                    
                        // Close the user query
                        $user_stmt->close();
                    } else {
                        echo "<tr><td colspan='10' class='text-center'>No transaction history available</td></tr>";
                    }
                    
                    // Close main query
                    $history_stmt->close();
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

<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>
<!-- Core Script -->
<script src="../scripts/indent.js" type='module'></script>
<script src="<?php echo getCacheBustedUrl('../scripts/notifications.js'); ?>" type="module"></script>
<script src="<?php echo getCacheBustedUrl('../scripts/game-payout.js'); ?>" type="module"></script>
</body>
</html>
