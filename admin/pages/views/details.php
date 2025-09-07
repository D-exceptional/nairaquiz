<?php 
require '../server/conn.php';
require '../session.php';

$uid = '';
$name = '';

// Get and sanitize incoming trialID
$trialID = $_GET['trialID'] ?? '';

if (!empty($trialID)) {
    // Step 1: Get userID from quiz_trials
    $trial_stmt = $conn->prepare("SELECT userID FROM quiz_trials WHERE trialID = ?");
    $trial_stmt->bind_param("i", $trialID);
    $trial_stmt->execute();
    $trial_result = $trial_stmt->get_result();

    if ($trial_result->num_rows > 0) {
        $val = $trial_result->fetch_assoc();
        $uid = $val['userID'];

        // Step 2: Get fullname from users table
        $user_stmt = $conn->prepare("SELECT fullname FROM users WHERE userID = ?");
        $user_stmt->bind_param("i", $uid);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();

        if ($user_result->num_rows > 0) {
            $user_data = $user_result->fetch_assoc();
            $name = $user_data['fullname'];
        }

        $user_stmt->close();
    }

    $trial_stmt->close();
}

//$conn->close();
?>

<!DOCTYPE html>
<html lang="en" style='overflow-x: hidden !important;width: 100vw;height: 100vh;'>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User | Trials</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- Include SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Sweetalert JS Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?php echo getCacheBustedUrl('../../dist/css/overlay.css'); ?>">
  <style>
      html, body{
          overflow: auto;
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
            <h1>
              <?php
                // Make sure $uid and $name are already defined and sanitized
                $total = 0;
                
                if (!empty($uid)) {
                    $stmt = $conn->prepare("SELECT COUNT(DISTINCT trial_date) AS total FROM quiz_trials WHERE userID = ?");
                    $stmt->bind_param("i", $uid);
                    $stmt->execute();
                    $stmt->bind_result($total);
                    $stmt->fetch();
                    $stmt->close();
                }
                
                echo "<b>$name ($total)</b>";
             ?>
            </h1>
          </div>
          <div class="col-sm-6">
            <!--<ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Trials</li>
            </ol>--->
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content" style='overflow-y: auto !important;box-sizing: border-box;'>
      <!-- Default box -->
      <div class="card" style='height: 550px;'>
        <div class="card-header">
          <h3 class="card-title">All Trials</h3>
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
                <th>Type</th>
                <th>Stake</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
                if (!empty($uid)) {
                    $stmt = $conn->prepare("
                        SELECT trial_points, trial_type, trial_stake, trial_date
                        FROM quiz_trials qt
                        INNER JOIN (
                            SELECT MAX(trialID) AS max_id
                            FROM quiz_trials
                            WHERE userID = ?
                            GROUP BY trial_date
                        ) latest ON qt.trialID = latest.max_id
                        WHERE qt.userID = ?
                        ORDER BY qt.trialID DESC
                    ");
                    
                    $stmt->bind_param("ii", $uid, $uid);
                    $stmt->execute();
                    $result = $stmt->get_result();
                
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $trial_points = $row['trial_points'];
                            $trial_type = $row['trial_type'];
                            $trial_stake = number_format($row['trial_stake'], 2, '.', ',');
                            $trial_date = $row['trial_date'];
                
                            // Determine pass/fail status
                            $status = ($trial_points == 1)
                                ? "<button class='btn btn-success btn-sm'>Passed</button>"
                                : "<button class='btn btn-danger btn-sm'>Failed</button>";
                
                            // Output table row
                            echo "
                                <tr>
                                    <td>#</td>
                                    <td>{$trial_type} Questions</td>
                                    <td>&#x20A6;$trial_stake</td>
                                    <td>$status</td>
                                    <td>$trial_date</td>
                                </tr>
                            ";
                        }
                    }
                
                    $stmt->close();
                }
                
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
