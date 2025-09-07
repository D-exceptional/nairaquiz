import { displaySuccess, displayInfo } from './export.js';
  
$("#addEmail").on("click", function () {
    const email = $("#subscriptionEmail").val();
    if (email === "") {
        displayInfo("Email field is empty !");
    }
    else{
        $.ajax({
            type: "POST",
            url: "./assets/server/check.php",
            data:  { email: email },
            dataType: 'json',
            success: function (response) {
                for (const key in response) {
                    if (Object.hasOwnProperty.call(response, key)) {
                        const content = response[key];
                        if (content === "Email has been added") {
                          displaySuccess(content);
                          $("#subscriptionEmail").val("");
                        } else {
                          displayInfo(content);
                          $("#subscriptionEmail").val("");
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