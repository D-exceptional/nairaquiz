<?php 
require '../server/conn.php';
require '../session.php';

$name = '';

// Get and sanitize incoming ambassadorID
$ambassadorID = $_GET['ambassadorID'] ?? '';

if (!empty($ambassadorID)) {
    $stmt = $conn->prepare("SELECT fullname FROM ambassadors WHERE ambassadorID = ?");
    $stmt->bind_param("i", $ambassadorID); // use "i" if ambassadorID is numeric
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['fullname'];
    }

    $stmt->close();
}

//$conn->close();
?>
 
<!DOCTYPE html>
<html lang="en" style='overflow-x: hidden !important;width: 100vw;height: 100vh;'>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ambassador | Referrals</title>
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
                $total = 0;
                
                if (!empty($ambassadorID)) {
                    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM referred_users WHERE ambassadorID = ?");
                    $stmt->bind_param("i", $ambassadorID); // Change to "i" if ambassadorID is an integer
                    $stmt->execute();
                    $stmt->bind_result($total);
                    $stmt->fetch();
                    $stmt->close();
                }
                
                echo "<b> {$name}'s Referrals ($total) </b>";
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
          <h3 class="card-title">All Referrals</h3>
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
                <th>Profile</th>
                <th>Fullname</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
                if (!empty($ambassadorID)) {
                    // Step 1: Get all userIDs referred by this ambassador
                    $stmt = $conn->prepare("SELECT userID FROM referred_users WHERE ambassadorID = ?");
                    $stmt->bind_param("i", $ambassadorID); // Use "i" if ambassadorID is numeric
                    $stmt->execute();
                    $result = $stmt->get_result();
                
                    if ($result->num_rows > 0) {
                        // Prepare the user detail query once (to reuse)
                        $user_stmt = $conn->prepare("SELECT fullname, email, contact, created_on FROM users WHERE userID = ?");
                        
                        while ($row = $result->fetch_assoc()) {
                            $userID = $row['userID'];
                
                            // Fetch user details for each referred user
                            $user_stmt->bind_param("i", $userID);
                            $user_stmt->execute();
                            $user_result = $user_stmt->get_result();
                
                            if ($user_result->num_rows > 0) {
                                $val = $user_result->fetch_assoc();
                                $fullname = $val['fullname'];
                                $email = $val['email'];
                                $contact = $val['contact'];
                                $date = $val['created_on'];
                
                                // Display the row
                                echo "
                                    <tr>
                                        <td>
                                          <ul class='list-inline'>
                                              <li class='list-inline-item'>
                                                  <img alt='Avatar' class='table-avatar' src='../../../assets/img/user.png' style='width: 50px;height: 50px;border-radius: 50%;'>
                                              </li>
                                          </ul>
                                        </td>
                                        <td>$fullname</td>
                                        <td>$email</td>
                                        <td>$contact</td>
                                        <td>$date</td>
                                    </tr>
                                ";
                            }
                        }
                        $user_stmt->close();
                    }
                    $stmt->close();
                    $conn->close();
                }
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
