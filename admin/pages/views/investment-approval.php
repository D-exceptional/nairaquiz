<?php 
  require '../server/conn.php';
  require '../session.php';
?> 

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Investment Approvals</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../../dist/css/overlay.css">
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
              <h1>
                <b>
                  <?php
                    $statusPending = 'Pending';
                
                    // Prepare the statement
                    $stmt = $conn->prepare("SELECT SUM(plan_amount) AS total_amount FROM investor_plans WHERE plan_status = ?");
                    if (!$stmt) {
                      die("Prepare failed: " . $conn->error); // Debug prepare error
                    }
                    $stmt->bind_param("s", $statusPending);
                    if (!$stmt->execute()) {
                      die("Execute failed: " . $stmt->error); // Debug execution error
                    }
                    $stmt->bind_result($total_amount);
                    $stmt->fetch();
                    $stmt->close();
                
                    // Format and display result
                    $naira = "&#x20A6;";
                    $formatted_amount = $naira . number_format($total_amount ?? 0, 2, '.', ',');
                    echo "Investment Approvals ($formatted_amount)";
                  ?>
                </b>
              </h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                <li class="breadcrumb-item active">Investment Approvals</li>
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
            <h3 class="card-title">Investment Approvals</h3>
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
                  <th>S/N</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Receipt</th>
                  <th>Plan</th>
                  <th>Amount</th>
                  <th>Currency</th>
                  <th>Date</th>
                  <th>Reference</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $status = 'Pending';
                  
                  $stmt = $conn->prepare("SELECT * FROM investor_plans WHERE plan_status = ? ORDER BY planID DESC");
                  $stmt->bind_param("s", $status);
                  $stmt->execute();
                  $result = $stmt->get_result();
                  
                  if ($result->num_rows > 0) {
                    // Prepare secondary statements once, outside the loop
                    $receipt_stmt = $conn->prepare("SELECT receipt_filename FROM investor_receipts WHERE receipt_reference = ?");
                    $investor_stmt = $conn->prepare("SELECT fullname, email FROM investors WHERE investorID = ?");
                    $currency_stmt = $conn->prepare("SELECT currency_type FROM investor_finance WHERE investorID = ?");
                
                    while ($value = $result->fetch_assoc()) {
                      $plan_id = $value['planID'];
                      $investor_id = $value['investorID'];
                      $plan_name = $value['plan_name'];
                      $plan_amount = $value['plan_amount'];
                      $plan_date = $value['plan_created'];
                      $reference = $value['plan_reference'];
              
                      $payment_amount = "&#x20A6;" . number_format($plan_amount, 2, '.', ',');
              
                      // Receipt image
                      $receipt_stmt->bind_param("s", $reference);
                      $receipt_stmt->execute();
                      $receipt_result = $receipt_stmt->get_result();
                      $receipt_data = $receipt_result->fetch_assoc();
                      $receipt_name = $receipt_data['receipt_filename'] ?? 'no-receipt.png';
              
                      // User info
                      $investor_stmt->bind_param("i", $investor_id);
                      $investor_stmt->execute();
                      $user_result = $investor_stmt->get_result();
                      $user_data = $user_result->fetch_assoc();
                      $fullname = $user_data['fullname'] ?? 'Unknown';
                      $email = $user_data['email'] ?? 'Unknown';
              
                      // Currency
                      $currency_stmt->bind_param("i", $investor_id);
                      $currency_stmt->execute();
                      $currency_result = $currency_stmt->get_result();
                      $currency_data = $currency_result->fetch_assoc();
                      $currency = $currency_data['wallet_currency'] ?? 'NGN';
              
                      // HTML content
                      $approve_btn = "<button class='btn btn-info btn-sm' style='margin-bottom: 10px;'>Approve</button>";
                      $delete_btn = "<button class='btn btn-danger btn-sm'>Delete</button>";
                      $status_btn = "<button class='btn btn-danger btn-sm'>Pending</button>";
                      $receipt_image = "<img src='../../../documents/$receipt_name' style='width: 100px; height: 100px; border-radius: 5px;' alt='Receipt Image'>";
                
                      echo "
                        <tr class='rows' id='$plan_id'>
                          <td>#</td>
                          <td class='name'>$fullname</td>
                          <td class='email'>$email</td>
                          <td class='receipt'>$receipt_image</td>
                          <td class='plan'>$plan_name</td>
                          <td class='amount'>$payment_amount</td>
                          <td class='currency'>$currency</td>
                          <td class='date'>$plan_date</td>
                          <td class='ref'>$reference</td>
                          <td class='status'>$status_btn</td>
                          <td class='action'>
                            $approve_btn
                            $delete_btn
                          </td>
                        </tr>
                      ";
                    }
                  
                    // Close all prepared statements after loop
                    $receipt_stmt->close();
                    $investor_stmt->close();
                    $currency_stmt->close();
                  } else {
                    echo "<tr><td colspan='11'><p style='text-align: center;'>No pending payments available</p></td></tr>";
                  }
                  
                  // Close main statement and DB connection
                  $stmt->close();
                  $conn->close();
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

  <div id="details-overlay">
    <img src="">
    <div id="close-view"><i class="fa fa-times" aria-hidden="true"></i></div>
  </div>

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
  <script src="<?php echo getCacheBustedUrl('../scripts/investment-approval.js'); ?>" type="module"></script>
</body>
</html>
