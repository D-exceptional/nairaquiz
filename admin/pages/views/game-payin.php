<?php 
  require '../server/conn.php';
  require '../session.php';
?> 
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Game Pay-ins</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
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
            <h1>
                <b>
                    <?php
                        // Prepared statement to get total completed fund amount
                        $status = 'Completed';
                        $stmt = $conn->prepare("SELECT SUM(fund_amount) AS total_amount FROM wallet_fund WHERE fund_status = ?");
                        $stmt->bind_param("s", $status);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($row = $result->fetch_assoc()) {
                            $total_amount = $row['total_amount'] ?? 0;
                        } else {
                            $total_amount = 0;
                        }
                        
                        $formatted_amount = "&#x20A6;" . number_format($total_amount, 2, '.', ',');
                        echo "Game Pay-ins ($formatted_amount)";
                        
                        $stmt->close();
                    ?>
                </b>
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Game Pay-ins</li>
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
          <h3 class="card-title">Game Pay-ins</h3>
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
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Reference</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                    // Fetch all wallet fund records
                    $fund_stmt = $conn->prepare("SELECT * FROM wallet_fund ORDER BY fundID DESC");
                    $fund_stmt->execute();
                    $fund_result = $fund_stmt->get_result();
                    
                    if ($fund_result->num_rows > 0) {
                        // Prepare the user info query once
                        $user_stmt = $conn->prepare("SELECT fullname FROM users WHERE userID = ?");
                    
                        while ($value = $fund_result->fetch_assoc()) {
                            $user_id = $value['userID'];
                            $amount = $value['fund_amount'];
                            $date = $value['fund_date'];
                            $reference = $value['fund_txref'];
                            $status = $value['fund_status'];
                    
                            // Format amount
                            $payment_amount = "&#x20A6;" . number_format($amount, 2, '.', ',');
                    
                            // Get user's fullname
                            $user_stmt->bind_param("i", $user_id);
                            $user_stmt->execute();
                            $user_result = $user_stmt->get_result();
                            $user_data = $user_result->fetch_assoc();
                            $fullname = $user_data['fullname'] ?? 'Unknown';
                    
                            // Format status button
                            $button = ($status === 'Pending') 
                                ? "<button class='btn btn-danger btn-sm'>Pending</button>"
                                : "<button class='btn btn-success btn-sm'>Completed</button>";
                    
                            $action = "<button class='btn btn-info btn-sm'>View</button>";
                    
                            // Output row
                            echo "
                                <tr class='rows'>
                                    <td>#</td>
                                    <td>$fullname</td>
                                    <td>$payment_amount</td>
                                    <td>$date</td>
                                    <td>$reference</td>
                                    <td>$button</td>
                                    <td>$action</td>
                                </tr>
                            ";
                        }
                    
                        // Close user query
                        $user_stmt->close();
                    } else {
                        echo "<tr><td colspan='10'><p style='text-align: center;'>No transaction history available</p></td></tr>";
                    }
                    
                    // Close main query
                    $fund_stmt->close();
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
</body>
</html>
