import { displaySuccess, displayInfo } from './export.js';

$(document).ready(function () {
      
  $("#details").on("blur", function () {
    const question = $(this).val();
    if (question === "") {
      displayInfo("Question field is empty!");
    }
    else{
      $.ajax({
          type: "POST",
          url: "../server/check.php",
          data:  { question: question },
          dataType: 'json',
          success: function (response) {
            for (const key in response) {
              if (Object.hasOwnProperty.call(response, key)) {
                const content = response[key];
                if (content === "You are good to go") {
                  displaySuccess(content);
                  $("#addQuestion").attr('disabled', false);
                } else {
                  displayInfo(content);
                  $("#addQuestion").attr('disabled', true);
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


  $("#addQuestion").on("click", function () {
    const details = $("#details").val();
    const opt1 = $("#opt1").val();
    const opt2 = $("#opt2").val();
    const opt3 = $("#opt3").val();
    const opt4 = $("#opt4").val();
    const answer = $("#answer").val();

    const data = {
      details: details,
      opt1: opt1,
      opt2: opt2,
      opt3: opt3,
      opt4: opt4,
      answer: answer
    }

    //console.log(data);

    if (!data.details || !data.opt1 || !data.opt2 || !data.opt3 || !data.opt4 || !data.answer) {
      displayInfo("Fill out all fields before submitting !");
    } else {
      $.ajax({
        type: "POST",
        url: "../server/add-question.php",
        data: data,
        dataType: "json",
        success: function (response) {
          for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
              const content = response.Info;
              if (content === "Question added sucsessfully") {
                displaySuccess(content);
                //Reset form
                $(".form-control").each(function (index, el) {
                  $(el).val("");
                });
              } else {
                displayInfo(content);
              }
            }
          }
        },
        error: function (e) {
          displayInfo(e.responseText);
        },
      });
    }
  });

  //Indent all inner child navs
  $(".nav-sidebar").addClass("nav-child-indent");

});
