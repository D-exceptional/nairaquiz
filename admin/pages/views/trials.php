<?php 
  require '../server/conn.php';
  require '../session.php';
?> 

<!DOCTYPE html>
<html lang="en" style='overflow-x: hidden !important;width: 100vw;height: 100vh;'>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Trials</title>
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
                $stmt = $conn->prepare("SELECT COUNT(*) FROM quiz_trials");
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->close();
                
                $total = number_format($count, 0, '.', ',');
                
                echo "<b> Trials ($total) </b>";
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
                <th>Fullname</th>
                <th>Trials</th>
                <th>Wins</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
               <?php
                    // Prepare and execute the optimized query using JOIN and GROUP BY
                    $query = "
                        SELECT 
                            u.userID,
                            u.fullname,
                            COUNT(DISTINCT qt.trial_date) AS total_trials,
                            SUM(CASE WHEN qt.trial_points = 1 THEN 1 ELSE 0 END) AS total_wins,
                            MAX(qt.trialID) AS last_trial_id
                        FROM quiz_trials qt
                        INNER JOIN users u ON qt.userID = u.userID
                        GROUP BY u.userID
                        ORDER BY total_wins DESC
                    ";
                    
                    $stmt = $conn->prepare($query);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    // Output table rows
                    if ($result->num_rows > 0) {
                        $counter = 1;
                        while ($row = $result->fetch_assoc()) {
                            $trialID = htmlspecialchars($row['last_trial_id']);
                            $fullname = htmlspecialchars($row['fullname']);
                            $total = (int)$row['total_trials'];
                            $wins = (int)$row['total_wins'];
                    
                            echo "
                                <tr class='rows' id='$trialID'>
                                    <td>{$counter}</td>
                                    <td class='name'>{$fullname}</td>
                                    <td>{$total}</td>
                                    <td>{$wins}</td>
                                    <td>
                                        <button class='btn btn-info btn-sm'>View</button>
                                    </td>
                                </tr>
                            ";
                            $counter++;
                        }
                    } else {
                        echo "<tr><td colspan='5'>No quiz trial records found.</td></tr>";
                    }
                    
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
 
<div id='trials-overlay' style='display: none;'>
   <div id='close-trials-overlay'><i class="fa fa-times" aria-hidden="true"></i></div>
   <iframe src="" frameborder="0"></iframe>
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
<script src="<?php echo getCacheBustedUrl('../scripts/trials.js'); ?>"></script>
</body>
</html>
