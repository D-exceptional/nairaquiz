import { displaySuccess, displayInfo } from "./export.js";

$(document).ready(function () {

  //Preview selected image
  function previewFile(input) {
    if (input.files && input.files[0]) {
      let extension = input.files[0].name.split(".").pop().toLowerCase();
      //let sizeCal = input.files[0].size / 1024 / 1024;
      switch (extension) {
        case "zip":
        case "jfif":
          displayInfo(
            "Selected file format not supported. Choose a file with either .jpg, .jpeg, .png, .pdf, .docx, .mp4 and .mp3 extension !"
          );
          $("#send-email").attr("disabled", true);
          break;
        case "jpg":
        case "jpeg":
        case "png":
        case "pdf":
        case "docx":
        case "mp3":
        case "mp4":
          displaySuccess("Included attachment is supported");
          $("#send-email").attr("disabled", false);
          break;
      }
    }
  }

  $("#attachment").on("change", function () {
    previewFile(this);
  });

  $("#send-email").on("click", function () {
    const recipient = $("#receiver").val();
    const subject = $("#subject").val();
    const message = $("#compose-textarea").val();
    const attachment = $("#attachment").val();

    if (recipient === "" || subject === "" || message === "") {
      displayInfo("Fill out all text fields before submitting !");
      $("#send-email").attr("disabled", false);
    } else {
      if (attachment === "" || attachment === null) {
        const decision = confirm("Send without an attachment ?");
        if (decision === true) {
          $("#send-email").text("Sending...").attr("disabled", true);
          //Prepare request parameters
          setTimeout(() => {
            $.ajax({
              type: "POST",
              url: "server/send-textmail.php",
              data: {
                recipient: recipient,
                subject: subject,
                message: message,
              },
              dataType: "json",
              success: function (response) {
                for (const key in response) {
                  if (Object.hasOwnProperty.call(response, key)) {
                    const content = response.Info;
                    if (content === "Message sent successfully") {
                      displaySuccess(content);
                      $(
                        "#receiver, #subject, #attachment, #compose-textarea"
                      ).val("");
                      $("#send-email")
                        .html("<i class='fas fa-check'></i> Sent")
                        .attr("disabled", true);
                      setTimeout(() => {
                        $("#send-email")
                          .html("<i class='far fa-envelope'></i> Send")
                          .attr("disabled", false);
                      }, 2000);
                      window.location.reload();
                    } else {
                      displayInfo(content);
                      $("#send-email")
                        .html("<i class='far fa-envelope'></i> Send")
                        .attr("disabled", false);
                    }
                  }
                }
              },
              error: function (e) {
                displayInfo(e.responseText);
                $("#send-email")
                  .html("<i class='far fa-envelope'></i> Send")
                  .attr("disabled", false);
              },
            });
          }, 4000);
        } else {
          displayInfo("Select a file to continue..");
        }
      } else {
        const request = new FormData();
        request.append("recipient", recipient);
        request.append("subject", subject);
        request.append("message", message);
        request.append(
          "attachment",
          document.getElementById("attachment").files[0]
        );
        $("#send-email").text("Sending...").attr("disabled", true);
        $.ajax({
          type: "POST",
          url: "server/send-mediamail.php",
          data: request,
          dataType: "json",
          processData: false,
          contentType: false,
          cache: false,
          success: function (response) {
            for (const key in response) {
              if (Object.hasOwnProperty.call(response, key)) {
                const content = response.Info;
                if (content === "Message sent successfully") {
                  displaySuccess(content);
                  $("#receiver, #subject, #attachment, #compose-textarea").val(
                    ""
                  );
                  $("#send-email")
                    .html("<i class='fas fa-check'></i> Sent")
                    .attr("disabled", true);
                  setTimeout(() => {
                    $("#send-email")
                      .html("<i class='far fa-envelope'></i> Send")
                      .attr("disabled", false);
                  }, 2000);
                  window.location.reload();
                } else {
                  displayInfo(content);
                  $("#send-email")
                    .html("<i class='far fa-envelope'></i> Send")
                    .attr("disabled", false);
                }
              }
            }
          },
          error: function (e) {
            displayInfo(e.responseText);
            $("#send-email")
              .html("<i class='far fa-envelope'></i> Send")
              .attr("disabled", false);
          },
        });
      }
    }
  });

  //Indent all inner child navs
  $(".nav-sidebar").addClass("nav-child-indent");
});
