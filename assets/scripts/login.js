import { displaySuccess, displayInfo } from './export.js';

$("#email").val('');
$("#password").val('');

// Toggle password visibility
$('#psw-div i').on("click", function() {
    togglePasswordVisibility($(this), 'password');
});

$('#new-psw-div i').on("click", function() {
    togglePasswordVisibility($(this), 're-password');
});

function togglePasswordVisibility($icon, type) {
    const $passwordInput = type === 'password' ? $("#password") : $("#new-password");
    
    const isHidden = $passwordInput.attr('type') === 'password';
    $passwordInput.attr('type', isHidden ? 'text' : 'password');
    
    $icon
        .removeClass(isHidden ? 'fa-eye' : 'fa-eye-slash')
        .addClass(isHidden ? 'fa-eye-slash' : 'fa-eye');
}

//Check All Inputs
$('.form-control').each(function (index, el) {
  $(el)
  .on('keyup, blur', function () {
    if ($(el).val() !== '') {
      $(el).css({'border':'none'});
      $('#loginUser').attr('disabled', false);
    }
    else {
      $(el).css({'border':'2px solid red'});
      $('#loginUser').attr('disabled', true);
    }
  });
});

//Quick password reset
$("#reset").on("click", function () {
 $("#psw-div, #info-p").css({ display: "none" });
 $("#email").val("");
 $("#loginUser").text("Verify email");
});

//Check status by email
$('#email')
.on('keyup', function () {
  if ($(this).val() === '') {
    displayInfo("No email supplied !");
    $("#loginUser").attr("disabled", true);
  }
  else{
    $("#loginUser").attr("disabled", false);
  }
});

let trials = 2;

function getOTP(val) {
  $.ajax({
    type: "POST",
    url: "./assets/server/otp.php",
    data: { email: val },
    dataType: "json",
    success: function (response) {
      const content = response.Info;
      if (content === "OTP Sent") {
        $("#email-div, #psw-div, #new-psw-div, #otp-div").css({
          display: "block",
        });
        $("#info-p").css({ display: "none" });
        $("#email").val(val);
       $("#loginUser").text("Update password");
      } else {
        displayInfo(content);
      }
    },
    error: function (e) {
      //displayInfo('Error connecting to server');
      displayInfo(e.responseText);
    },
  });
}

function login() {
  const email = $("#email").val();
  const password = $("#password").val();

  if (email === "" || password === "") {
    displayInfo("Some fields are empty !");
  }
  else{
    $.ajax({
      type: "POST",
      url: "./assets/server/login.php",
      data:  { email: email, password: password },
      dataType: 'json',
      success: function (response) {
        const content = response.Info;
        if (content === "You have successfully logged in") {
          $("#login-form")[0].reset();
          //Redirect to dashboard
          window.location = response.user.link;
        } else if (content === "Check your email or password again") {
          displayInfo(content);
          /*Update counter
          trials--;
          if (trials === 0) {
            //Show modal
            setTimeout(() => {
              displayInfo("You have entered incorrect password 2 times");
            }, 4000);
            setTimeout(function () {
              displayInfo("It seems you forgot your password; reset it now");
              $("#psw-div, #info-p").css({ display: "none" });
              $("#email").val("");
              $("#loginUser").text("Verify email");
            }, 7000);
          }*/
        } else {
          displayInfo(content);
          return;
        }
      },
      error: function (e) {
        //displayInfo('Error connecting to server');
        displayInfo(e.responseText);
      }
    });
  }
}

function verify() {
  const email = $("#email").val();
  if (email == "") {
    displayInfo("Enter your email");
  }
  else{
    getOTP(email);
  }
}

function update() {
  const password = $("#password").val();
  const changedPassword = $("#new-password").val();
  const otp = $("#otp").val();
  
  if(otp == ""){
    displayInfo("Enter the OTP sent to your email address");
    return; 
  }
  else if(password == ""){
    displayInfo("Type a password");
    return;
  }
  else if(changedPassword == ""){
     displayInfo("Re-type password");
    return;
  }
  if (password !== changedPassword) {
    displayInfo("Passwords do not match");
    return;
  }
  else{
    $.ajax({
      type: "POST",
      url: "./assets/server/password.php",
      data: {
        email: $("#email").val(),
        password: changedPassword,
        otp: otp
      },
      dataType: "json",
      success: function (response) {
        const content = response.Info;
        if (content === "Password changed successfully") {
          displaySuccess(content);
          setTimeout(function () {
            $("#psw-div, #email-div, #info-p").css({
              display: "block",
            });
            $("#new-psw-div, #otp-div").css({ display: "none" });
            $("#loginUser").text("Login");
          }, 2000);
        } else {
          displayInfo(content);
        }
      },
      error: function () {
        displayInfo("Error connecting to server");
      },
    });
  }
}

$("#login-form").on("submit", function(e){
  e.preventDefault();
  let loginButtonText = $('#loginUser').text();
  switch (loginButtonText) {
    case 'Login':
      login();
    break;
    case 'Verify email':
      verify();
    break;
    case 'Update password':
      update();
    break;
  }
});
