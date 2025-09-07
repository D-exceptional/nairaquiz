<?php 
  require '../server/conn.php';
  require '../session.php';
?> 

<!DOCTYPE html>
<html lang="en" style='overflow-x: hidden !important;width: 100vw;height: 100vh;'>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Sessions</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Include SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Sweetalert JS Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?php echo getCacheBustedUrl('../../dist/css/overlay.css'); ?>">
  <style>
      html, body{
          overflow-x: hidden;
      }
  </style>
</head>
<body class="hold-transition sidebar-mini" id='<?php echo $_SESSION['userID']; ?>'>
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
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM session_game");
                    $stmt->execute();
                    $stmt->bind_result($count);
                    $stmt->fetch();
                    $stmt->close();
                
                    echo "<b> Session Games ($count) </b>";
             ?>
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active"> Sessions</li>
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
          <h3 class="card-title">Sessions</h3>
          <div class="card-tools">
            <select name="filter" id="filter" style="margin-right: 5px;">
             <option value="25" selected>25</option>
             <option value="50">50</option>
             <option value="100">100</option>
             <option value="250">250</option>
             <option value="500">500</option>
             <option value="1000">1000</option>
             <option value="All">All</option>
           </select>
           <button type="button" class="btn btn-success btn-sm" id='refresh'>
               <i class='fa fa-refresh' aria-hidden="true" style="padding-right: 5px;"></i>
               Refresh
           </button>
          </div>
        </div>
        <div class="card-body p-0" style='overflow-x: auto !important;white-space: normal;'>
          <table class="table table-striped projects">
            <thead>
              <tr>
                <th>Session Name</th>
                <th>Session Question</th>
                <th>Session Answer</th>
                <th>Session Date</th>
                <th>Session Status</th>
                <th>Total Players</th>
                <th>Total Amount</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
                <?php
                    $stmt = $conn->prepare("
                        SELECT 
                            sg.sessionID,
                            sg.session_name,
                            sg.session_question,
                            sg.session_answer,
                            sg.session_date,
                            sg.session_status,
                            COUNT(DISTINCT sp.userID) AS total_players
                        FROM session_game sg
                        LEFT JOIN session_players sp ON sg.session_name = sp.session_name
                        WHERE sg.session_status = 'Pending'
                        GROUP BY sg.sessionID
                        HAVING total_players > 0
                        ORDER BY sg.sessionID DESC
                        LIMIT 25
                    ");
                    
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $sessionID = htmlspecialchars($row['sessionID']);
                            $session_name = htmlspecialchars($row['session_name']);
                            $session_question = htmlspecialchars($row['session_question']);
                            $session_answer = htmlspecialchars($row['session_answer']);
                            $session_date = htmlspecialchars($row['session_date']);
                            $total_players = (int)$row['total_players'];
                            $total_amount = '₦' . number_format(($total_players * 1000) ?? 0, 2, '.', ',');
                    
                            // Always Pending because of the SQL filter
                            $status = "<button type='button' class='btn btn-danger btn-sm'>Pending</button>";
                            $action = "<button type='button' class='btn btn-info btn-sm'>Finalize</button>";
                    
                            // Output row
                            echo "
                                <tr class='rows' id='{$sessionID}'>
                                    <td class='name'>{$session_name}</td>
                                    <td class='question'>{$session_question}</td>
                                    <td class='answer'>{$session_answer}</td>
                                    <td class='date'>{$session_date}</td>
                                    <td class='status'>{$status}</td>
                                    <td class='total'>{$total_players}</td>
                                    <td class='total'>{$total_amount}</td>
                                    <td class='action'>{$action}</td>
                                </tr>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='10'><p style='text-align: center;'>No pending sessions with players found.</p></td></tr>";
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
 
<!--- Overlay Open -->
<div id='details-overlay' style='display: none;'>
  <iframe src="../views/question-add.php" frameborder="0"></iframe>
  <div id='close-view'>X</div>
</div>
<!--- Overlay Close --->

<div id="loading-overlay" style='display: none;'>
    <div id="shine"></div>
    <i class="fas fa-spinner loader"></i>
    <!--<span class="loading-text">Loading...</span>-->
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
<script src="<?php echo getCacheBustedUrl('../scripts/multiplayer.js'); ?>" type="module"></script>
</body>
</html>
