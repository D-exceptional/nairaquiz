<?php 
  require '../server/conn.php';
  require '../session.php';
?> 

<!DOCTYPE html>
<html lang="en" style='overflow-x: hidden !important;width: 100vw;height: 100vh;'>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Questions</title>
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
                $stmt = $conn->prepare("SELECT COUNT(*) FROM questions");
                $stmt->execute();
                $stmt->bind_result($count);
                $stmt->fetch();
                $stmt->close();
                
                $total = number_format($count, 0, '.', ',');
            
                echo "<b>Questions ($total)</b>";
              ?>
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active"> Questions</li>
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
          <h3 class="card-title">Questions</h3>
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
           <input type="file" id="file" accept=".pdf" hidden />
           <button type="button" class="btn btn-success btn-sm" id='upload'>
               <i class='fa fa-upload' aria-hidden="true" style="padding-right: 5px;"></i>
               PDF
           </button>
           <button type="button" class="btn btn-success btn-sm" style='margin-left: 5px;' id='open-overlay'>Add</button>
          </div>
        </div>
        <div class="card-body p-0" style='overflow-x: auto !important;white-space: normal;'>
          <table class="table table-striped projects">
            <thead>
              <tr>
                <th>Question</th>
                <th>A</th>
                <th>B</th>
                <th>C</th>
                <th>D</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
               <?php
                // Prepare the query with LIMIT
                $stmt = $conn->prepare("SELECT questionID, question_details, option_one, option_two, option_three, option_four FROM questions ORDER BY questionID DESC LIMIT ?");
                $limit = 25;
                $stmt->bind_param('i', $limit);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $questionID = $row['questionID'];
                        $question_details = htmlspecialchars($row['question_details']);
                        $option_one = htmlspecialchars($row['option_one']);
                        $option_two = htmlspecialchars($row['option_two']);
                        $option_three = htmlspecialchars($row['option_three']);
                        $option_four = htmlspecialchars($row['option_four']);
                
                        echo "
                            <tr class='rows' id='$questionID'>
                              <td class='question' tabindex='0'>$question_details</td>
                              <td class='option_one' tabindex='0'>$option_one</td>
                              <td class='option_two' tabindex='0'>$option_two</td>
                              <td class='option_three' tabindex='0'>$option_three</td>
                              <td class='option_four' tabindex='0'>$option_four</td>
                              <td>
                                <button class='btn btn-danger btn-sm'>Delete</button>
                              </td>
                            </tr>
                        ";
                    }
                } else {
                    echo "No questions available!";
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
<!-- Include SweetAlert JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
<script src="../scripts/indent.js" type='module'></script>
<script src="<?php echo getCacheBustedUrl('../scripts/notifications.js'); ?>" type="module"></script>
<script src="<?php echo getCacheBustedUrl('../scripts/question.js'); ?>" type="module"></script>
</body>
</html>
