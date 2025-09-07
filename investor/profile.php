<?php 
  require 'server/conn.php';
  require 'session.php';
  
  function getWalletDetails(mysqli $conn, int $investorID): ?array {
    $stmt = $conn->prepare("SELECT account_number, bank_name FROM investor_finance WHERE investorID = ?");
    $stmt->bind_param("i", $investorID);

    if ($stmt->execute()) {
      $result = $stmt->get_result();
      if ($result->num_rows > 0) {
        $wallet = $result->fetch_assoc();
        $stmt->close();
        return [
          'account' => $wallet['account_number'],
          'bank' => $wallet['bank_name'],
        ];
      }
    }

    $stmt->close();
    return null; // No wallet found or error occurred
  }
  
  $walletDetails = getWalletDetails($conn, $investorID);
    if ($walletDetails) {
      $account_number = $walletDetails['account'];
      $bank = $walletDetails['bank'];
    } 
    
  $bank_array = array();

  function getCountryCurrency($countryName) {
    $countryName = urlencode($countryName); // URL encode the country name
    $url = "https://restcountries.com/v3.1/name/{$countryName}?fullText=true"; // Restcountries API endpoint

    // Initialize cURL session
    $ch = curl_init();
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // For HTTPS
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // For HTTPS

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check for errors
    if(curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
        curl_close($ch);
        return null;
    }
    
    // Close cURL session
    curl_close($ch);
    
    // Decode JSON response
    $data = json_decode($response, true);

    // Check if we got a result
    if (isset($data[0]['currencies'])) {
        // Extract currency information
        $currencies = $data[0]['currencies'];
        $currencyCode = array_key_first($currencies);
        $currencyName = $currencies[$currencyCode]['name'];
        return $currencyCode;
    } else {
        return null;
    }
  }

  // Example usage
  $currency = getCountryCurrency($country);

?>  
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Investor | Profile</title>
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
            <h1><b>Settings</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Home</a></li>
              <li class="breadcrumb-item active">Investor Settings</li>
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

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                <?php
                    echo "<img src='$profile' class='profile-user-img img-fluid img-circle' style='width: 100px !important;height: 100px !important;' alt='Admin Image'>";
                ?>
                </div>

                <h3 class="profile-username text-center">
                <?php
                   echo "<a href='#' class='d-block' style='overflow: hidden !important;white-space: nowrap !important;text-overflow: ellipsis !important;'>$fullname</a>";
                  ?>
                </h3>
                <p class="text-muted text-center">Investor</p>
                <a href="#" class="btn btn-primary btn-block" id='update-profile'><b>Update</b></a>
                <input type="file" class="form-control" id="profile-image" hidden>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Details</a></li>
                  <li class="nav-item"><a class="nav-link" href="#bank" data-toggle="tab">Earnings</a></li>
                  <li class="nav-item"><a class="nav-link" href="#security" data-toggle="tab">Security</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="tab-pane" id="settings" style="display: block;">
                    <form class="form-horizontal" id='details-form'>
                      <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" name="name" value='<?php echo $fullname; ?>' placeholder="Name" disabled>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="email" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                          <input type="email" class="form-control" name="email" value='<?php echo $email; ?>' placeholder="Email" disabled>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="contact" class="col-sm-2 col-form-label">Contact</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" name="contact" value='<?php echo $contact; ?>' placeholder="Contact" disabled>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="bank">
                    <form class="form-horizontal" id='bank-form'>
                         <div class="form-group row">
                          <label for="facebook" class="col-sm-2 col-form-label">Currency</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" id='Currency' name="currency" value="<?php echo $currency; ?>" placeholder="Currency" disabled>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="facebook" class="col-sm-2 col-form-label">Bank Name</label>
                          <div class="col-sm-10">
                           <?php 
                                echo '<select class="form-control" name="bank" id="bankName">';

                                  $secret_key = 'sk_live_dd519ff2272708c948e5e92b4149029ab52328ca';
                                  $curl = curl_init();
                                  curl_setopt_array($curl, array(
                                      CURLOPT_URL => "https://api.paystack.co/bank?country=$country",
                                      CURLOPT_RETURNTRANSFER => true,
                                      CURLOPT_ENCODING => "",
                                      CURLOPT_MAXREDIRS => 10,
                                      CURLOPT_TIMEOUT => 30,
                                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                      CURLOPT_CUSTOMREQUEST => "GET",
                                      CURLOPT_HTTPHEADER => array(
                                          "Authorization: Bearer $secret_key",
                                          "Cache-Control: no-cache",
                                        ),
                                  ));

                                  $response = curl_exec($curl);
                                  $err = curl_error($curl);
                                  curl_close($curl);

                                  if ($err) {
                                      echo '<option>Error loading banks</option>';
                                  } else {
                                      global $bank;
                                      //Append current bank
                                      if ($bank !== 'None') {
                                        echo "<option value='$bank'>$bank</option>";
                                      }
                                      $result = json_decode($response, true);
                                      $result_data = $result['data'];
                                      // Loop through each object in the array
                                      foreach ($result_data as $value) {
                                          $bank_name = $value['name']; 
                                          $bank_code = $value['code']; 
                                          $bank_info = array(
                                            'name' => $bank_name,
                                            'code' => $bank_code
                                          );
                                          //Update array
                                          array_push($bank_array, $bank_info);
                                          //Display banks
                                          echo "<option value='$bank_name'>$bank_name</option>";
                                      }
                                  }

                                echo '</select>';

                                // Convert PHP array to JSON
                                $json_data = json_encode($bank_array);
                                // Echo JSON data
                                echo '<script>';
                                echo "const jsonData = $json_data;";
                                echo '</script>';
                            ?>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="facebook" class="col-sm-2 col-form-label">Account Number</label>
                          <div class="col-sm-10">
                            <?php 
                              echo "<input type='number' class='form-control' name='account_number' value='$account_number' id='accountNumber' placeholder='Account Number'>";
                            ?>
                          </div>
                        </div>
                        <div class="form-group row" id='accountNameDiv' style='display: none;'>
                          <label for="facebook" class="col-sm-2 col-form-label">Account Name</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" name="account_name" id="accountName" placeholder="Account Name" disabled>
                          </div>
                        </div>
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10" id="bank-button-action">
                          <?php 
                            if($bank === 'None'){
                            echo "<button type='submit' id='add-details' class='btn btn-success'>Add</button>";
                            }
                            else{
                            echo "<button type='submit' id='add-details' class='btn btn-success'>Update</button>";
                            }
                          ?>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- Security section -->
                  <div class="tab-pane" id="security">
                    <form class="form-horizontal" id='security-form'>
                        <div class="form-group row">
                          <label for="facebook" class="col-sm-2 col-form-label">Current Password</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" id='currentPassword' name="current_password" placeholder="Current password">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="facebook" class="col-sm-2 col-form-label">New Password</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" id='changePassword' name="new_password" placeholder="New password">
                          </div>
                        </div>
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button type="submit" class="btn btn-success">Update</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
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
      <b>Version</b> 3.1.0
    </div>
    <strong>Copyright &copy; 2014-2021 <a href="#">chromstack</a>.</strong> All rights reserved.
  </footer>-->

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
<!-- Core Script -->
<script src="scripts/indent.js" type='module'></script>
<script src="<?php echo getCacheBustedUrl('scripts/notifications.js'); ?>" type="module"></script>
<script src="<?php echo getCacheBustedUrl('scripts/profile.js'); ?>" type='module'></script>
<script src="<?php echo getCacheBustedUrl('scripts/bank-details.js'); ?>" type='module'></script>
<script src="<?php echo getCacheBustedUrl('scripts/security.js'); ?>" type='module'></script>
</body>
</html>
