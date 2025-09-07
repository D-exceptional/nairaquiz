<?php 
  require 'server/conn.php';
  require 'session.php';
?> 

<!DOCTYPE html>
<html lang="en" style='overflow-x: hidden !important;width: 100vw;height: 100vh;'>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Investor | Investments</title>
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
            <h1 style='color: #212529'><b>Investments</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Home</a></li>
              <li class="breadcrumb-item active">Investments</li>
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
          <h3 class="card-title">Investments</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-success btn-sm" style='margin-left: 5px;' id='create-investment'>Create New Investment</button>
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
                <th>Plan</th>
                <th>Amount</th>
                <th>ROI</th>
                <th>Reference</th>
                <th>Status</th>
                <th>Created</th>
                <th>Updated</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
                // Prepare the statement
                $stmt = $conn->prepare("SELECT * FROM investor_plans WHERE investorID = ? ORDER BY planID ASC");
                $stmt->bind_param("i", $investorID);
                  
                if ($stmt->execute()) {
                  $result = $stmt->get_result();
                
                  if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                      $plan = htmlspecialchars($row['plan_name']);
                      $amount = '₦' . number_format($row['plan_amount'], 2, '.', ',');
                      $roi = htmlspecialchars($row['plan_roi']);
                      $reference = htmlspecialchars($row['plan_reference']);
                      $created = htmlspecialchars($row['plan_created']);
                      $updated = htmlspecialchars($row['plan_updated']);
                      $status = $row['plan_status'] === 'Pending' ? "<button class='btn btn-danger btn-sm'>Pending</button>" : "<button class='btn btn-success btn-sm'>Active</button>";
                      $action = "<button class='btn btn-info btn-sm'>View</button>";
          
                      // Output table row
                      echo "
                        <tr class='rows'>
                          <td>#</td>
                          <td>$plan</td>
                          <td>$amount</td>
                          <td>$roi</td>
                          <td>$reference</td>
                          <td>$status</td>
                          <td>$created</td>
                          <td>$updated</td>
                          <td>$action</td>
                        </tr>
                      ";
                    }
                  }
                  else {
                    echo "<tr><td colspan='10'><p style='text-align: center;'>No investments available</p></td></tr>";
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
</body>
</html>
