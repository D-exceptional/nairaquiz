import { displaySuccess, displayInfo } from "../scripts/export.js";

//Edit Bio Data
$('#security-form').on('submit', function (event) {
    event.preventDefault();
    if ($('#currentPassword').val() == '' || $('#changePassword').val() == '') {
        displayInfo('Some fields are empty');
    }
    else {
        $.ajax({
            type: "POST",
            url: "../server/update-password.php",
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                for (const key in response) {
                    if (Object.hasOwnProperty.call(response, key)) {
                        const content = response.Info;
                        if (content === "Password updated successfully") {
                          displaySuccess(content);
                          setTimeout(function () {
                            window.location.reload();
                          }, 1500);
                        } else {
                          displayInfo(content);
                        }
                    }
                }
            },
            error: function (e) {
                displayInfo(e.responseText);
            }
        });
    }
});

