import { displaySuccess, displayInfo } from './export.js';

//Get the URL
const queryString = new URL(window.location);
// We can then parse the query string’s parameters using URLSearchParams:
const urlParams = new URLSearchParams(queryString.search);
//Then we call any of its methods on the result.
const email = urlParams.get('email');
const input = document.createElement('input');
    input.type = 'text';
    input.id = 'email';
    input.name = 'email';
    input.value = email;
    input.setAttribute('hidden', true);
$("#update-form").append(input);

// Toggle password visibility
$(document).on("click", '.fas.fa-lock', function() {
    togglePasswordVisibility(this);
});

//Check All Inputs
$('.form-control').each(function (index, el) {
    $(el).on('keyup', function () {
        if ($(el).val() !== '') {
            $(el).css({'border':'none'});
            $('#update').attr('disabled', false);
        }
            else {
            $(el).css({'border':'2px solid red'});
            $('#update').attr('disabled', true);
        }
    })
    .on('blur', function () {
        if ($(el).val() !== '') {
            $(el).css({'border':'none'});
            $('#update').attr('disabled', false);
        }
            else {
            $(el).css({'border':'2px solid red'});
            $('#update').attr('disabled', true);
        }
    });
});

function update() {
    let password = $("#password").val();
    let changedPassword = $("#confirm-password").val();
  
    if (password !== changedPassword) {
        displayInfo("Passwords do not match");
        return;
    }
    else{
        $.ajax({
            type: "POST",
            url: "server/change-password.php",
            data:  { email: $("#email").val(), password: changedPassword },
            dataType: 'json',
            success: function (response) {
                const content = response.Info;
                if ( content === "Password changed successfully") {
                  displaySuccess(content);
                  setTimeout(function () {
                    window.location = response.page.link;
                  }, 5000);
                } else {
                  displayInfo(content);
                }
            },
            error: function (e) {
                displayInfo(e.responseText);
            }
        });
    }
}

function togglePasswordVisibility(iconElement) {
    const $icon = $(iconElement);
    const $container = $icon.closest('.input-group.mb-3'); // Wrap with $
    const $passwordInput = $container.find('input');

    const isHidden = $passwordInput.attr('type') === 'password';
    $passwordInput.attr('type', isHidden ? 'text' : 'password');
}

$("#update-form").on("submit", function(e){
    e.preventDefault();
   update();
});
