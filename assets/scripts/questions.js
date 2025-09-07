import { displaySuccess, displayInfo } from './export.js';

$(document).ready(function () {
  //Get the URL
  const queryString = new URL(window.location);
  // We can then parse the query string’s parameters using URLSearchParams:
  const urlParams = new URLSearchParams(queryString.search);
  //Then we call any of its methods on the result.
  const id = urlParams.get("UID");

  //Check All Inputs
  $(".form-control").each(function (index, el) {
    $(el)
      .on("keyup", function () {
        if ($(el).val() !== "") {
          $(el).css({ border: "none" });
          $("#addQuestion").attr("disabled", false);
        } else {
          $(el).css({ border: "2px solid red" });
          $("#addQuestion").attr("disabled", true);
        }
      })
      .on("blur", function () {
        if ($(el).val() !== "") {
          $(el).css({ border: "none" });
          $("#addQuestion").attr("disabled", false);
        } else {
          $(el).css({ border: "2px solid red" });
          $("#addQuestion").attr("disabled", true);
        }
      });
  });

  $("#question").on("blur", function () {
    const question = $(this).val();
    if (question === "") {
      displayInfo("Question field is empty!");
    } else {
      $.ajax({
        type: "POST",
        url: "assets/server/lookup.php",
        data: { question: question },
        dataType: "json",
        success: function (response) {
          for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
              const content = response[key];
              if (content === "You are good to go") {
                displaySuccess(content);
                $("#addQuestion").attr("disabled", false);
              } else {
                displayInfo(content);
                $("#addQuestion").attr("disabled", true);
              }
            }
          }
        },
        error: function () {
          displayInfo("Error connecting to server");
        },
      });
    }
  });

  $("#question-form").on("submit", function (e) {
    e.preventDefault();
    
    const question = $("#question").val();
    const opt1 = $("#option1").val();
    const opt2 = $("#option2").val();
    const opt3 = $("#option3").val();
    const opt4 = $("#option4").val();
    const answer = $("#answer").val();

    const data = {
      id: id,
      question: question,
      opt1: opt1,
      opt2: opt2,
      opt3: opt3,
      opt4: opt4,
      answer: answer
    };

    if (
      !data.question ||
      !data.opt1 ||
      !data.opt2 ||
      !data.opt3 ||
      !data.opt4 ||
      !data.answer
    ) {
      displayInfo("Fill out all fields before submitting !");
      return;
    } else {
      $.ajax({
        type: "POST",
        url: "assets/server/add.php",
        data: data,
        dataType: "json",
        success: function (response) {
          for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
              const content = response[key];
              if (content === "Question added successfully") {
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
});
