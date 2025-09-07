<?php 
  require '../server/conn.php';
  require '../session.php';
?>  

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Investors</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Include SweetAlert CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- Include SweetAlert CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <!-- Sweetalert JS Script -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
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
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM investors");
                    $stmt->execute();
                    $stmt->bind_result($count);
                    $stmt->fetch();
                    $stmt->close();
                
                    echo "Investors ($count)";
                ?>
              </b>
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Investors</li>
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
          <h3 class="card-title">All Investors</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" style="background: green;color: white;" id="load-more">
              Load More
            </button>
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
                      <th>Profile</th>
                      <th>Email</th>
                      <th>Contact</th>
                      <th>Country</th>
                      <th>Created</th>
                      <th>Status</th>
                      <th>Action</th>
                  </tr>
              </thead>
              <tbody>
                <?php
                    $sql = "
                        SELECT 
                            i.investorID,
                            i.fullname,
                            i.email,
                            i.contact,
                            i.country,
                            i.created_on,
                            i.investor_status
                        FROM investors i
                        ORDER BY i.fullname ASC
                        LIMIT 50
                    ";
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $investorID = $row['investorID'];
                            $fullname = $row['fullname'];
                            $email = $row['email'];
                            $contact = $row['contact'];
                            $country = $row['country'];
                            $date = $row['created_on'];
                            $status = $row['investor_status'];
                    
                            $buttonClass = ($status === 'Pending') ? 'danger' : 'success';
                            $button = "<button class='btn btn-$buttonClass btn-sm'>$status</button>";
                            $action = "
                                <div style='display: flex; gap: 10px;'>
                                    <button class='btn btn-info btn-sm'>View</button>
                                    <button class='btn btn-danger btn-sm'>Delete</button>
                                </div>
                            ";
                    
                            echo "
                                <tr class='rows' id='$investorID'>
                                  <td>#</td>
                                  <td class='name'>" . htmlspecialchars($fullname) . "</td>
                                  <td>
                                    <ul class='list-inline'>
                                      <li class='list-inline-item'>
                                        <img alt='Avatar' class='table-avatar' src='../../../assets/img/user.png' style='width: 50px;height: 50px;border-radius: 50%;'>
                                      </li>
                                    </ul>
                                  </td>
                                  <td class='email'>" . htmlspecialchars($email) . "</td>
                                  <td>" . htmlspecialchars($contact) . "</td>
                                  <td>" . htmlspecialchars($country) . "</td>
                                  <td>$date</td>
                                  <td>$button</td>
                                  <td class='action'>$action</td>
                                </tr>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='9'><p style='text-align: center;'>No investors yet!</p></td></tr>";
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
<script src="<?php echo getCacheBustedUrl('../scripts/investors.js'); ?>" type="module"></script>
</body>
</html>
