<?php 
  require '../server/conn.php';
  require '../session.php';
?> 
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Compose Message</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?php echo getCacheBustedUrl('../../dist/css/mail-overlay.css'); ?>">
  <!-- summernote -->
  <link rel="stylesheet" href="../../plugins/summernote/summernote-bs4.min.css">
  <!-- Include SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Sweetalert JS Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="hold-transition sidebar-mini">
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
            <h1><b>Compose</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Compose</li>
              <li class="breadcrumb-item active" style="display: none;" id="admin-email"><?php echo $email; ?></li>
              <li class="breadcrumb-item active" style="display: none;" id="admin-name"><?php echo $fullname; ?></li>
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
                    <a href="#" class="nav-link">
                      <i class="fas fa-inbox"></i> Inbox
                      <span class="badge bg-primary float-right">
                       <?php
                        // Ensure $conn is your active DB connection and $email is defined properly
                        
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM mailbox");
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
                  <a href="../sent-mail.php" class="nav-link">
                    <i class="far fa-envelope"></i> Sent
                    <span class="badge bg-primary float-right">
                      <?php
                        // Ensure $conn is your active DB connection and $email is defined properly
                        
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM mailbox WHERE mail_sender = ?");
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
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title">Compose New Message</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="form-group">
                  <input class="form-control" placeholder="To:" id='choose-recipient'>
                </div>
                <div class="form-group">
                  <input class="form-control" placeholder="Subject:" id='subject'>
                </div>
                <div class="form-group">
                    <textarea id="compose-textarea" class="form-control" style="height: 300px"></textarea>
                </div>
                <div class="form-group">
                  <div class="btn btn-default btn-file">
                    <i class="fas fa-paperclip"></i> Attachment
                    <input type="file" id='attachment'>
                  </div>
                  <p class="help-block">Max. 32MB</p>
                </div>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <div class="float-right">
                  <!--<button type="button" class="btn btn-default"><i class="fas fa-pencil-alt"></i> Draft</button>-->
                  <button type="button" class="btn btn-primary" id='send-email'><i class="far fa-envelope"></i> Send</button>
                </div>
                <button type="button" class="btn btn-default" id='discard-email'><i class="fas fa-times"></i> Discard</button>
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
  <!-- /.content-wrapper --
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 1.1.0
    </div>
    <strong>Copyright &copy; 2023 <a href="#!">chromstack</a>.</strong> All rights reserved.
  </footer>-->
  
    <div class='mail-overlay'>
      <div class='main-content'>
          <div class='main-content-select'>
              <div id='view-admins'>Admins</div>
              <div id='view-ambassadors'>Ambassadors</div>
              <div id='view-users'>Users</div>
              <div class='main-content-close' id='close-modal'><i class="fa fa-times" aria-hidden="true"></i></div>
          </div>
          <div class='main-content-header'>
              <div class='main-content-header-text'>
                  Send to everyone
              </div>
              <div class='main-content-header-icon'>
                  <input type='checkbox' id='send-to-all'>
              </div>
          </div>
          <div class='main-content-view'>
              <div class='main-content-inner'>
                <div class='user-view' id='admin-section-view'>
                    <div class='user-view-header'>
                      <div class='user-view-header-text'>Admins Only</div>
                      <div class='user-view-header-icon'>
                          <input type='checkbox' id='send-all-admin'>
                      </div>
                    </div>
                  <div class='user-scroll' id='admin-section'>
                    <?php 
                        // Prepare the statement
                        $stmt = $conn->prepare("SELECT userID, fullname, email FROM users WHERE user_type = ? ORDER BY fullname ASC");
                        $user_type = 'Admin';
                        $stmt->bind_param("s", $user_type);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()){
                                $id = $row['userID'];
                                $admin_email = $row['email'];
                                $admin_name = $row['fullname'];
                        
                                echo " <div class='user-card'>
                                          <div class='user-card-image'>
                                              <img src='../../../assets/img/user.png' alt='admin-image'>
                                          </div>
                                          <div class='user-card-name'>$admin_name</div>
                                          <div class='user-card-select'>
                                              <input type='checkbox' class='add-user' id='$id'>
                                              <p class='email-address'>$admin_email</p>
                                          </div>
                                      </div>
                                    ";
                            }
                        } else {
                            echo "<center>No admin found</center>";
                        }
                        
                        $stmt->close();
                        //$conn->close();
                    ?>
                  </div>
                </div>
                <div class='user-view' id='ambassador-section-view'>
                  <div class='user-view-header'>
                      <div class='user-view-header-text'>Ambassadors Only</div>
                      <div class='user-view-header-icon'>
                          <input type='checkbox' id='send-all-ambassador'>
                      </div>
                    </div>
                  <div class='user-scroll' id='ambassador-section'>
                    <?php 
                        // Prepare the statement
                        $stmt = $conn->prepare("SELECT ambassadorID, fullname, email FROM ambassadors ORDER BY fullname ASC");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()){
                                $id = $row['ambassadorID'];
                                $ambassador_email = $row['email'];
                                $ambassador_name = $row['fullname'];
                        
                                echo " <div class='user-card'>
                                          <div class='user-card-image'>
                                              <img src='../../../assets/img/user.png' alt='admin-image'>
                                          </div>
                                          <div class='user-card-name'>$ambassador_name</div>
                                          <div class='user-card-select'>
                                              <input type='checkbox' class='add-user' id='$id'>
                                              <p class='email-address'>$ambassador_email</p>
                                          </div>
                                      </div>
                                    ";
                            }
                        } else {
                            echo "<center>No admin found</center>";
                        }
                        
                        $stmt->close();
                        //$conn->close();
                    ?>
                  </div>
                </div>
                <div class='user-view' id='user-section-view'>
                  <div class='user-view-header'>
                      <div class='user-view-header-text'>Users Only</div>
                      <div class='user-view-header-icon'>
                          <input type='checkbox' id='send-all-user'>
                      </div>
                    </div>
                  <div class='user-scroll' id='user-section'>
                    <?php 
                        // Prepare the statement
                        $stmt = $conn->prepare("SELECT userID, fullname, email FROM users WHERE user_type = ? ORDER BY fullname ASC");
                        $user_type = 'User';
                        $stmt->bind_param("s", $user_type);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()){
                                $id = $row['userID'];
                                $user_email = $row['email'];
                                $user_name = $row['fullname'];
                        
                                echo " <div class='user-card'>
                                          <div class='user-card-image'>
                                              <img src='../../../assets/img/user.png' alt='admin-image'>
                                          </div>
                                          <div class='user-card-name'>$user_name</div>
                                          <div class='user-card-select'>
                                              <input type='checkbox' class='add-user' id='$id'>
                                              <p class='email-address'>$user_email</p>
                                          </div>
                                      </div>
                                    ";
                            }
                        } else {
                            echo "<center>No admin found</center>";
                        }
                        
                        $stmt->close();
                        $conn->close();
                    ?>
                  </div>
                </div>
              </div>
          </div>
      </div>
  </div>

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
<!-- Summernote -->
<script src="../../plugins/summernote/summernote-bs4.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>
<!-- Page specific script -->
<script>
  $(function () {
    //Add text editor
    $('#compose-textarea').summernote()
  })
</script>
<!-- Core Script -->
<script src="../scripts/indent.js" type='module'></script>
<script src="<?php echo getCacheBustedUrl('../scripts/notifications.js'); ?>" type="module"></script>
<script src="<?php echo getCacheBustedUrl('../scripts/compose-mail.js'); ?>" type='module'></script>
</body>
</html>
