<?php 
  require '../server/conn.php';
  require '../session.php';
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
  <link rel="stylesheet" href="../../admin/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../admin/dist/css/adminlte.min.css">
  <!-- Include SweetAlert CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <!-- Sweetalert JS Script -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <h1>
              <?php 
                // Get total quiz trials for user
                $totalStmt = $conn->prepare("SELECT COUNT(DISTINCT trial_date) FROM quiz_trials WHERE userID = ?");
                $totalStmt->bind_param("i", $userID);
                $totalStmt->execute();
                $totalStmt->bind_result($total);
                $totalStmt->fetch();
                $totalStmt->close();

                
                // Get total wins (trial_points = 1) for user
                $winsStmt = $conn->prepare("SELECT COUNT(*) FROM quiz_trials WHERE userID = ? AND trial_points = 1");
                $winsStmt->bind_param("i", $userID);
                $winsStmt->execute();
                $winsStmt->bind_result($wins);
                $winsStmt->fetch();
                $winsStmt->close();
                
                // Output result
                echo "<b>Trials ($total), Wins ($wins)</b>";
              ?>
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Trials</li>
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
          <h3 class="card-title">Trials</h3>
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
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
               <?php
               /*
                If multiple entries have the same trial_date, only the latest one (based on trialID) should be returned.
                Keep one unique record per trial_date.
               */
                $stmt = $conn->prepare("
                    SELECT qt.trialID, qt.trial_type, qt.trial_points, qt.trial_date
                    FROM quiz_trials qt
                    INNER JOIN (
                        SELECT MAX(trialID) AS max_id
                        FROM quiz_trials
                        WHERE userID = ?
                        GROUP BY trial_date
                    ) latest_trials ON qt.trialID = latest_trials.max_id
                    WHERE qt.userID = ?
                    ORDER BY qt.trialID DESC
                ");
                $stmt->bind_param("ii", $userID, $userID); // Bind twice
                $stmt->execute();
                $result = $stmt->get_result();
            
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $trialID = $row['trialID'];
                        $trial_type = htmlspecialchars($row['trial_type']);
                        $trial_point = $row['trial_points'];
                        $trial_date = htmlspecialchars($row['trial_date']);
                
                        // Determine pass/fail status
                        $status = ($trial_point == 1) ? "<button class='btn btn-success btn-sm'>Passed</button>" : "<button class='btn btn-danger btn-sm'>Failed</button>";
                
                        echo "
                            <tr class='rows' id='$trialID'>
                                <td>#</td>
                                <td>$trial_type</td>
                                <td>$status</td>
                                <td>$trial_date</td>
                                <td>
                                    <button class='btn btn-info btn-sm'>View</button>
                                </td>
                            </tr>
                        ";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No trials available!</td></tr>";
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
<script src="<?php echo getCacheBustedUrl('../scripts/notifications.js'); ?>" type="module"></script>
</body>
</html>
