<?php 
  require '../server/conn.php';
  require '../session.php';
  
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
  <title>User | Wallet</title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../admin/plugins/fontawesome-free/css/all.min.css">
  <!-- Include SweetAlert CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <!-- Sweetalert JS Script -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Theme style -->
  <link rel="stylesheet" href="../../admin/dist/css/adminlte.min.css">
  <link href="<?php echo getCacheBustedUrl('../../assets/css/modal.css'); ?>" rel="stylesheet">
  <link href="<?php echo getCacheBustedUrl('../../admin/dist/css/overlay.css'); ?>" rel="stylesheet">
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
                $stmt = $conn->prepare("SELECT wallet_amount FROM wallet WHERE userID = ?");
                $stmt->bind_param("i", $userID);
                $stmt->execute();
                $stmt->bind_result($wallet_amount_result);
                
                if ($stmt->fetch()) {
                  $wallet_amount = number_format($wallet_amount_result, 2, '.', ',');
                } else {
                  $wallet_amount = number_format(0, 2, '.', ',');
                }
                
                $stmt->close();
                
                // Show result
                echo "<b style='color: black;'> Wallet Balance (₦$wallet_amount) </b>";
              ?>
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Wallet Balance</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content" style='overflow-y: auto !important;box-sizing: border-box;'>
      <div class="row" style='height: 550px;'>
        <?php
        if ($country != "Nigeria") {
          echo " 
            <div class='col-md-6'>
            <div class='card card-primary'>
              <div class='card-header'>
                <h3 class='card-title'>International Payment Guide</h3>
                <div class='card-tools'>
                  <button type='button' class='btn btn-tool' data-card-widget='collapse' title='Collapse'>
                    <i class='fas fa-minus'></i>
                  </button>
                </div>
              </div>
              <div class='card-body'>
                <span>
                  <b>Payment Instructions for USDT</b>
                  <br>
                  <br>
                  Thank you for choosing to play our quiz game! To complete your payment, please follow these steps:
                  <br>
                  <br>
                  1. Send the exact amount of USDT required for your participation to our wallet address:
                  <br>
                  <br>
                  Wallet Address: <b id='wallet-address' style='cursor: pointer;'>0xdc7bf804566bb1dde4955cd9678b8bcc455fe247</b>
                  <br>
                  Network: <b>Arbitrum one</b>
                  <br>
                  Currency: <b>USDT</b>
                  <br>
                  <br>
                  <b>IMPORTANT: Our flat rate is as follows:</b>
                  <br>
                  <br>
                  <b>1 USDT = ₦1000</b>
                  <br>
                  <b>2 USDT = ₦2000</b>
                  <br>
                  <b>3 USDT = ₦3000</b>
                  <br>
                  ...and so on.
                  <br>
                  <br>
                  To avoid any delays in verification of payments,
                  <br>
                  1. When filling out the payment form, please ensure that the amount you enter in Naira matches our flat rate.
                  <br>
                  2. Once you've sent the payment, please fill out the form below with your transaction details.
                  <br>
                  3. Click the 'Submit' button to complete your payment. Our team will verify your transaction and update your account
                  balance accordingly.
                  <br>
                  <br>
                  <b>Note:</b>
                  <br>
                  <br>
                  - Ensure you send the payment from a wallet that supports USDT transactions.
                  <br>
                  - Payments made without the correct transaction details may be delayed or rejected.
                  <br>
                  - Any attempts to exploit our flat rate or manipulate the payment system will result in penalties or account suspension.
                  <br>
                  - If the exchange rate does not tally with your transfer, we cannot honor the payment. 
                  <br>
                  - Ensure that the amount you send in USDT corresponds to the correct amount in Naira according to our flat rate.
                  <br>
                  <br>
                  If you have any questions or concerns, contact our support team.
                </span>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>";
        }
        ?>

        <div class="col-md-6">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Fund Wallet</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="adminName">Full Name</label>
                <input type="text" id="name" class="form-control" value="<?php echo $fullname; ?>" disabled>
              </div>
              <div class="form-group">
                <label for="adminContact">Contact</label>
                <input type="text" id="contact" class="form-control" value="<?php echo $contact; ?>" disabled>
              </div>
              <div class="form-group">
                <label for="adminEmail">Email</label>
                <input type="email" id="email" class="form-control" value="<?php echo $email; ?>" disabled>
              </div>
              <div class="form-group">
                <label for="country">Country</label>
                <input type="text" id="country" class="form-control" value='<?php echo $country; ?>' disabled>
              </div>
              <div class="form-group">
                <label for="currency">Currency</label>
                <input type="text" id="currency" class="form-control" value='<?php echo $currency; ?>' disabled>
              </div>
              <div class="form-group">
                <label for="amount">Amount (All payments are in <b>₦</b>)</label>
                <input type="number" id="amount" class="form-control" value=''>
              </div>
              <div class="row">
                <div class="col-12">
                  <!--<input type="button" id="fund" value="Fund Wallet" class="btn btn-success float-left">-->
                  <input type="button" id="topup" value="Fund Wallet" class="btn btn-success float-left">
                </div>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
      </div>
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

<!-- Payment Modal -->
<div id='payment-modal-overlay'>
  <div class="row g-4" style="width: 80%;">
    <div class="container">
      <div class="text-center" style="margin-bottom: 40px;padding-top: 5rem;">
        <h1 class="mb-5">Make Payment</h1>
      </div>
      <br>
      <div style="margin: 20px 0px 40px 0px;">
        <center>
          <img src='' alt='logo' id='bankLogo'>
        </center>
      </div>
    </div>
    <br>
    <div class="col-lg-6 col-md-12 wow fadeInUp" data-wow-delay="0.5s">
      <form id="payment-form">
        <div class="row g-3" style='padding: 0% 2%;'>
          <center>
            <p id='pay-text'></p>  
          </center>
          <div class="col-12" style="display: flex;flex-direction: column;align-items: center;justify-content: center;margin: 5px 0px 50px 0px;">
            <p><b>Attach Payment Receipt Here</b></p>
            <input type="file" id="file" class="form-control">
          </div>
          <div class="col-12">
            <button class="btn btn-primary w-100 py-3" type="button" id="pay" style="color: white;" disabled>Submit</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div id='payment-overlay-close'><i class="fas fa-times"></i></div>
</div>
    
<div id="details-overlay">
  <img src="">
  <div id="close-view"><i class="fas fa-times"></i></div>
</div>

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

<!-- Paystack Payments -->
<script src="https://js.paystack.co/v2/inline.js"></script>
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
<!--<script src="../scripts/wallet.js" type='module'></script>-->
<script src="<?php echo getCacheBustedUrl('../scripts/pay.js'); ?>" type='module'></script>
</body>
</html>

