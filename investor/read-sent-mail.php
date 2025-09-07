<?php
  require 'server/conn.php';
  require 'session.php';
  
  if (isset($_GET['mailID'])) {
    $mailID = $_GET['mailID'];

    // Prepare and execute the statement
    $stmt = $conn->prepare("SELECT * FROM investor_mailbox WHERE mailID = ?");
    $stmt->bind_param("i", $mailID);

    if ($stmt->execute()) {
      $row = $stmt->get_result();
      if ($row->num_rows > 0) {
        $result = $row->fetch_assoc(); // Make $row globally available
      } else {
        $result = null;
      }
    } else {
      $result = null;
    }

    $stmt->close();
  } else {
    $result = null;
  }
?> 
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Investor | Read Mail</title>
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
            <h1><b>Read Mail</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Home</a></li>
              <li class="breadcrumb-item active">Read Mail</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content" style='overflow-y: auto !important;box-sizing: border-box;'>
      <div class="container-fluid">
        <div class="row" style='height: 550px;'>
          <div class="col-md-3">
            <a href="mailbox.php" class="btn btn-primary btn-block mb-3">Back to Inbox</a>

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Folders</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body p-0">
                <ul class="nav nav-pills flex-column">
                  <li class="nav-item active">
                    <a href="mailbox.php" class="nav-link">
                      <i class="fas fa-inbox"></i> Inbox
                      <span class="badge bg-primary float-right">
                        <?php
                          // Ensure $conn is your active DB connection and $email is defined properly
                          $stmt = $conn->prepare("SELECT COUNT(*) FROM investor_mailbox WHERE mail_receiver = ?");
                          $stmt->bind_param("s", $email);
                          $stmt->execute();
                          $stmt->bind_result($count);
                          $stmt->fetch();
                          $stmt->close();
                          
                          echo $count;
                        ?>
                      </span>
                    </a>
                  </li>
                <li class="nav-item">
                  <a href="sent-mail.php" class="nav-link">
                    <i class="far fa-envelope"></i> Sent
                    <span class="badge bg-primary float-right">
                      <?php
                        // Ensure $conn is your active DB connection and $email is defined properly
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM investor_mailbox WHERE mail_sender = ?");
                        $stmt->bind_param("s", $fullname);
                        $stmt->execute();
                        $stmt->bind_result($count);
                        $stmt->fetch();
                        $stmt->close();
                        
                        echo $count;
                      ?>
                    </span>
                  </a>
                </li>
              </ul>
              </div>
              <!-- /.card-body -->
            </div>
          </div>
          <!-- /.col -->
        <div class="col-md-9">
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title">Read Mail</h3>
              <div class="card-tools">
                <a href="#" class="btn btn-tool" title="Previous"><i class="fas fa-chevron-left"></i></a>
                <a href="#" class="btn btn-tool" title="Next"><i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
              <div class="mailbox-read-info">
                <h6>From: <b><?php echo $result['mail_sender'] ?></b>
                  <span class="mailbox-read-time float-right"><?php echo $result['mail_date'].'  '.$result['mail_time'] ?></span></h6>
              </div>
              <!-- /.mailbox-read-info -->
              
              <!-- /.mailbox-controls -->
              <div class="mailbox-read-message">
                <p><?php echo $result['mail_message'] ?></p>
              </div>
              <!-- /.mailbox-read-message -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer bg-white">
              <ul class="mailbox-attachments d-flex align-items-stretch clearfix">
                <?php 
                  $sql = mysqli_query($conn, "SELECT mail_filename, mail_extension FROM investor_mailbox WHERE mailID = '$mailID'");   
                  if(mysqli_num_rows($sql) > 0){
                    while($row = mysqli_fetch_assoc($sql)){
                      $filename = $row['mail_filename'];
                      $extension = $row['mail_extension'];
                      //Filter media
                      switch ($extension) {
                        case 'pdf':
                          echo " <li>
                                  <span class='mailbox-attachment-icon'><i class='far fa-file-pdf'></i></span>
                                  <div class='mailbox-attachment-info'>
                                    <a href='../attachments/$filename' class='mailbox-attachment-name'><i class='fas fa-paperclip'></i> $filename</a>
                                        <span class='mailbox-attachment-size clearfix mt-1'>
                                          <span>1,245 KB</span>
                                          <a href='../attachments/$filename' class='btn btn-default btn-sm float-right' download><i class='fas fa-cloud-download-alt'></i></a>
                                        </span>
                                  </div>
                                </li>
                              ";
                        break;
                        case 'docx':
                          echo "<li>
                                  <span class='mailbox-attachment-icon'><i class='far fa-file-word'></i></span>
                                  <div class='mailbox-attachment-info'>
                                    <a href='../attachments/$filename' class='mailbox-attachment-name'><i class='fas fa-paperclip'></i> $filename</a>
                                        <span class='mailbox-attachment-size clearfix mt-1'>
                                          <span>1,245 KB</span>
                                          <a href='../attachments/$filename' class='btn btn-default btn-sm float-right' download><i class='fas fa-cloud-download-alt'></i></a>
                                        </span>
                                  </div>
                                </li>
                                ";
                        break;
                        case 'jpg':
                        case 'jpeg':
                        case 'png':
                          echo " <li>
                                    <span class='mailbox-attachment-icon has-img'><img src='../attachments/$filename' alt='Image'></span>
                                    <div class='mailbox-attachment-info'>
                                      <a href='#' class='mailbox-attachment-name'><i class='fas fa-camera'></i> $filename</a>
                                          <span class='mailbox-attachment-size clearfix mt-1'>
                                            <span>2.67 MB</span>
                                            <a href='../attachments/$filename' class='btn btn-default btn-sm float-right' download><i class='fas fa-cloud-download-alt'></i></a>
                                          </span>
                                    </div>
                                  </li>
                              ";
                        break;
                        case 'mp4':
                          echo " <li>
                                    <span class='mailbox-attachment-icon has-img'><video src='../attachments/$filename' controls='true' style='max-height: 133px;'></video></span>
                                    <div class='mailbox-attachment-info'>
                                      <a href='#' class='mailbox-attachment-name'><i class='fas fa-camera'></i> $filename</a>
                                          <span class='mailbox-attachment-size clearfix mt-1'>
                                            <span>2.67 MB</span>
                                            <a href='../attachments/$filename' class='btn btn-default btn-sm float-right' download><i class='fas fa-cloud-download-alt'></i></a>
                                          </span>
                                    </div>
                                  </li>
                              ";
                        break;
                      }
                    }
                  }
                  else{
                    echo "<h3>No attachment available for this email</p>";
                  }
                ?>  
              </ul>
            </div>
            <!-- /.card-footer -->
            <div class="card-footer">
              <!--<div class="float-right">
                <button type="button" class="btn btn-default"><i class="fas fa-reply"></i> Reply</button>
                <button type="button" class="btn btn-default"><i class="fas fa-share"></i> Forward</button>
              </div>
              <button type="button" class="btn btn-default"><i class="far fa-trash-alt"></i> Delete</button>
              <button type="button" class="btn btn-default"><i class="fas fa-print"></i> Print</button>--->
            </div>
            <!-- /.card-footer -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.1.0
    </div>
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">chromstack</a>.</strong> All rights reserved.
  </footer> -->
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
<script src="scripts/indent.js"></script>
<script src="<?php echo getCacheBustedUrl('scripts/notifications.js'); ?>" type="module"></script>
</body>
</html>
