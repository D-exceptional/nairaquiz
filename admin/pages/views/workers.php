<?php 
  require '../server/conn.php';
  require '../session.php';
?>  

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Workers</title>
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
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM workers");
                    $stmt->execute();
                    $stmt->bind_result($count);
                    $stmt->fetch();
                    $stmt->close();
                
                    echo "Workers ($count)";
                ?>
              </b>
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Workers</li>
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
          <h3 class="card-title">All Workers</h3>
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
                      <th>Name</th>
                      <th>Profile</th>
                      <th>Email</th>
                      <th>Contact</th>
                      <th>Country</th>
                      <th>Created</th>
                      <th>Status</th>
                      <th>Total</th>
                      <th>Link</th>
                      <th>Action</th>
                  </tr>
              </thead>
              <tbody>
                <?php 
                    $workerStmt = $conn->prepare("SELECT workerID, fullname, email, contact, country, created_on, worker_status FROM workers ORDER BY fullname ASC");
                    $workerStmt->execute();
                    $workerResult = $workerStmt->get_result();
                    
                    if ($workerResult->num_rows > 0) {
                        $questionStmt = $conn->prepare("SELECT SUM(total_points) AS total_questions FROM upload_track WHERE workerID = ?");
                        
                        while ($result = $workerResult->fetch_assoc()) {
                            $workerID = $result['workerID'];
                            $fullname = $result['fullname'];
                            $email = $result['email'];
                            $contact = $result['contact'];
                            $country = $result['country'];
                            $date = $result['created_on'];
                            $status = $result['worker_status'];
                    
                            // Use prepared statement correctly
                            $questionStmt->bind_param("i", $workerID);
                            $questionStmt->execute();
                            $questionStmt->bind_result($total_questions_uploaded);
                            $questionStmt->fetch();
                            $total_questions_uploaded = $total_questions_uploaded ?? 0;
                            // ❌ DO NOT call store_result() here
                    
                            $buttonClass = ($status === 'Pending') ? 'danger' : 'success';
                            $button = "<button class='btn btn-$buttonClass btn-sm'>$status</button>";
                    
                            $link = "<button class='btn btn-info btn-sm' style='width: 250px;'>https://nairaquiz.com/work?UID=$workerID</button>";
                            $action = "<button class='btn btn-danger btn-sm'>Delete</button>";
                    
                            echo "
                                <tr class='rows' id='$workerID'>
                                  <td class='name'>$fullname</td>
                                  <td>
                                    <ul class='list-inline'>
                                      <li class='list-inline-item'>
                                        <img alt='Avatar' class='table-avatar' src='../../../assets/img/user.png' style='width: 50px;height: 50px;border-radius: 50%;'>
                                      </li>
                                    </ul>
                                  </td>
                                  <td>$email</td>
                                  <td>$contact</td>
                                  <td>$country</td>
                                  <td>$date</td>
                                  <td>$button</td>
                                  <td>$total_questions_uploaded</td>
                                  <td>$link</td>
                                  <td>$action</td>
                                </tr>
                            ";
                        }
                    
                        $questionStmt->close();
                    } else {
                        echo "<p style='position: absolute;transform: translate(-50%, -50%);top: 50%;left: 50%;'>No workers yet!</p>";
                    }
                    
                    $workerStmt->close();
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
<!-- Core files -->
<script src="../scripts/indent.js" type='module'></script>
<script src="<?php echo getCacheBustedUrl('../scripts/notifications.js'); ?>" type="module"></script>
<script src="<?php echo getCacheBustedUrl('../scripts/workers.js'); ?>" type="module"></script>
</body>
</html>
