import { displayInfo } from './export.js';

 $("#email").val('').focus();
 $("#password").val('');
 
 // Toggle password visibility
$('#psw-span').on("click", function() {
    togglePasswordVisibility();
});

//Check All Inputs
$('.form-control').each(function (index, el) {
    $(el).on('keyup', function () {
        if ($(el).val() !== '') {
            $(el).css({'border':'none'});
            $('#login').attr('disabled', false);
        }
            else {
            $(el).css({'border':'2px solid red'});
            $('#login').attr('disabled', true);
        }
    })
    .on('blur', function () {
        if ($(el).val() !== '') {
            $(el).css({'border':'none'});
            $('#login').attr('disabled', false);
        }
            else {
            $(el).css({'border':'2px solid red'});
            $('#login').attr('disabled', true);
        }
    });
});

var trials = 5;

function login() {
    let email = $("#email").val();
    let password = $("#password").val();
  
    if (email == "" || password == "") {
      displayInfo("Some fields are empty !");
    }
    else{
        $.ajax({
            type: "POST",
            url: "server/login.php",
            data:  { email: email, password: password },
            dataType: 'json',
            success: function (response) {
                const content = response.Info;
                if (content === "You have successfully logged in") {
                  //Redirect to dashboard
                  window.location = response.admin.link;
                } else if (
                  content === "No record found" ||
                  content === "All inputs must be filled out"
                ) {
                  displayInfo(content);
                } else {
                  displayInfo(content);
                  trials--;
                  if (trials === 0) {
                    //Show modal
                    setTimeout(() => {
                      displayInfo(
                        "You have entered incorrect password 2 times"
                      );
                    }, 4000);
                    setTimeout(function () {
                      displayInfo(
                        "It seems you forgot your password; reset it now via the link below"
                      );
                    }, 7000);
                  }
                }
            },
            error: function (e) {
                displayInfo(e.responseText);
            }
        });
    }
}

function togglePasswordVisibility() {
    const $passwordInput = $("#password");
    const isHidden = $passwordInput.attr('type') === 'password';
    $passwordInput.attr('type', isHidden ? 'text' : 'password');
}

$("#login-form").on("submit", function(e){
    e.preventDefault();
   login();
});
