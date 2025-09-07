<?php 
  require 'server/conn.php';
  require 'session.php';

  // Get plan details
  function getPlanDetails($id, $conn) {
    $stmt = $conn->prepare("SELECT * FROM investment_plans WHERE planID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row;
  }
?>  

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Investor | Plans</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- Include SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Sweetalert JS Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <h1><b>Investment Plans</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Home</a></li>
              <li class="breadcrumb-item active">Investment Plans</li>
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
          <h3 class="card-title">All Plans</h3>
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
                <th>Plan</th>
                <th>Amount</th>
                <th>ROI</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
                // Fetch all downlines
                $fetch_stmt = $conn->prepare("SELECT * FROM investment_plans");
                $fetch_stmt->execute();
                $fetch_result = $fetch_stmt->get_result();
                
                if ($fetch_result->num_rows > 0) {
                  while ($row = $fetch_result->fetch_assoc()) {
                    $planID = $row['planID'];

                    // Get plan details
                    $planDetails = getPlanDetails($planID, $conn);
                    $planName = $planDetails['plan_name'];
                    $planAmount = "&#x20A6;" . number_format($planDetails['plan_amount'], 2, '.', ',');
                    $planRoi = $planDetails['plan_roi'];
                    
                    // Get link name
                    $planParts = explode(' ',  $planName);
                    $linkName = $planParts[0];
            
                    // Format status button
                    $status = ($planDetails['plan_status'] === 'Pending') 
                        ? "<button class='btn btn-danger btn-sm'>Pending</button>"
                        : "<button class='btn btn-success btn-sm'>Active</button>";
            
                    $action = "<button class='btn btn-info btn-sm' data-link='https://nairaquiz.com/enroll?id=$investorID&package=$linkName' style='width: 100px;'>Copy Link</button>";
            
                    // Output row
                    echo "
                      <tr class='rows'>
                        <td>#</td>
                        <td>$planName</td>
                        <td>$planAmount</td>
                        <td>$planRoi</td>
                        <td>$status</td>
                        <td>$action</td>
                      </tr>
                    ";
                  }
                } else {
                  echo "<tr><td colspan='10'><p style='text-align: center;'>No plan available</p></td></tr>";
                }
                
                // Close main query
                $fetch_stmt->close();
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
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- Core files -->
<!--<script src="../assets/js/sweetalert2.all.min.js"></script>-->
<script src="scripts/indent.js" type='module'></script>
<script src="<?php echo getCacheBustedUrl('scripts/notifications.js'); ?>" type="module"></script>
<script src="<?php echo getCacheBustedUrl('scripts/plans.js'); ?>" type="module"></script>
</body>
</html>
