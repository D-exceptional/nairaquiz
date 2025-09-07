import { displaySuccess, displayInfo } from "../scripts/export.js";

// Update Profile Picture
function updateProfile() {
    const request = new FormData();
    const fileInput = document.getElementById('profile-image');
    
    if (fileInput.files.length === 0) {
        displayInfo("Please select a file to upload.");
        return;
    }

    request.append('profile', fileInput.files[0]);

    $.ajax({
        type: "POST",
        enctype: 'multipart/form-data',
        url: "server/update-profile.php",
        data: request,
        dataType: 'json',
        processData: false,
        contentType: false,
        cache: false,
        success: function (response) {
            const content = response.Info;
            if (content === "Profile updated successfully") {
                displaySuccess(content);
                setTimeout(() => window.location.reload(), 1500);
            } else {
                displayInfo(content);
            }
        },
        error: function (e) {
            displayInfo("An error occurred. Please try again.");
            console.error(e.responseText);
        }
    });
}

// Preview Profile Image
function previewProfileImage(input) {
    if (input.files && input.files[0]) {
        const extension = input.files[0].name.split('.').pop().toLowerCase();
        const allowedExtensions = ['jpg', 'jpeg', 'png'];

        if (allowedExtensions.includes(extension)) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $('.text-center img').attr('src', e.target.result);
                updateProfile();
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            displayInfo('Selected file is not an image!');
            $('.text-center img').attr('src', '../uploads/user.png');
        }
    }
}

// Toggle Profile Preview
$('#profile-image').on('change', function () {
    previewProfileImage(this);
});

// Trigger file input from button
$('#update-profile').on('click', function () {
    $('#profile-image').click();
});

// Navigation toggling
$('.nav-item a').on('click', function () {
    const linkText = $(this).text().trim();

    $('#settings, #bank, #security').hide();

    switch (linkText) {
        case "Details":
            $('#settings').show();
            break;
        case "Earnings":
            $('#bank').show();
            break;
        case "Security":
            $('#security').show();
            break;
    }
});
