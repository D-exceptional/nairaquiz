import { displaySuccess, displayInfo } from './export.js';

//Check All Inputs

$('.form-control').each(function (index, el) {
    $(el).on('keyup', function () {
        if ($(el).val() !== '') {
            $(el).css({'border':'none'});
            $('#sendMessage').attr('disabled', false);
        }
        else {
            $(el).css({'border':'2px solid red'});
            $('#sendMessage').attr('disabled', true);
        }
    })
    .on('blur', function () {
        if ($(el).val() !== '') {
            $(el).css({'border':'none'});
            $('#sendMessage').attr('disabled', false);
        }
        else {
            $(el).css({'border':'2px solid red'});
            $('#sendMessage').attr('disabled', true);
        }
    });
});

$("#contact-form").on("submit", function(e){
    e.preventDefault();
    const name = $("#name").val();
    const email = $("#email").val();
    const subject = $("#subject").val();
    const message = $("#message").val();
  
    if (name === "" ||  email === "" || subject === "" || message === "") {
        displayInfo("Fill out all fields before submitting !");
    }
    else{
        $.ajax({
            type: "POST",
            url: "./assets/server/contact.php",
            data:  { name: name, email: email, subject: subject, message: message },
            dataType: 'json',
            success: function (response) {
                for (const key in response) {
                    if (Object.hasOwnProperty.call(response, key)) {
                        const content = response[key];
                        if (content === "Message sent successfully") {
                          displaySuccess(content);
                          $(".form-control").each(function (index, el) {
                            $(el).val("");
                          });
                        } else {
                          displayInfo(content);
                        }
                    }
                }
            },
            error: function () {
                displayInfo('Error connecting to server');
            }
        });
    }
});

