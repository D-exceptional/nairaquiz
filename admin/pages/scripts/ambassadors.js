import { displaySuccess, displayInfo } from "../scripts/export.js";

function updateCount() {
  //Get count
  const counter = Number($("tbody").children("tr").length);
  $(".col-sm-6 h1")
    .empty()
    .html(`<b>Ambassadors (${counter})</b>`);
}

//Delete a course
function deleteAmbassador(name, id) {
  const promptMessage = confirm(
    "Are you sure to delete ambassador: " + '" ' + name + ' "' + " ?"
  );
  if (promptMessage === true) {
    $.ajax({
      type: "POST",
      url: "../server/ambassadors.php",
      data: { id: id },
      dataType: "json",
      success: function (response) {
        for (var key in response) {
          if (Object.hasOwnProperty.call(response, key)) {
            const content = response[key];
            if (content === "Ambassador deleted successfully") {
              $(".rows").each(function () {
                if ($(this).attr("id") === id) {
                  $(this).remove();
                  setTimeout(() => {
                    updateCount();
                  }, 1000);
                }
              });
              displaySuccess(content);
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
  } else {
    $("tbody").css({ opacity: "1" });
  }
}

//Copy link
$(".link .btn.btn-info").each(function (index, el) {
  $(el).on("click", function () {
    // Get the button text
    const buttonText = $(this).text();
    const textInput = document.createElement("input");
    textInput.setAttribute("type", "text");
    textInput.setAttribute("value", buttonText);
    textInput.setAttribute("hidden", true);
    textInput.select();
    textInput.setSelectionRange(0, 99999);
    const shareLink = textInput.value;
    navigator.clipboard.writeText(shareLink);
    displaySuccess("Link copied");
  });
});

//View stats
$(".action .btn.btn-info").each(function (index, el) {
  $(el).on("click", function () {
    const id = $(el).parent().parent().attr("id");
    $("#trials-overlay").css({ display: "block" });
    $("#trials-overlay iframe").attr("src", `../views/stats.php?ambassadorID=${id}`);
  });
});

//Delete worker
$(".action .btn.btn-danger").each(function (index, el) {
  $(el).on("click", function () {
    const name = $(el).parent().parent().find(".name").text();
    const id = $(el).parent().parent().attr("id");
    deleteAmbassador(name, id);
  });
});

//Indent all inner child navs
$(".nav-sidebar").addClass("nav-child-indent");

//Search function
$("#page-search").on("keyup", function () {
  let searchValue = $(this).val();
  if (searchValue !== "") {
    $(".rows").each(function (index, el) {
      if (
        $(el).find("td").text().toLowerCase().includes(searchValue)
      ) {
        $(el).css({ display: "table-row" });
      } else {
        $(el).css({ display: "none" });
      }
    });
  } else {
    $(".rows").each(function (index, el) {
      if ($(el).css("display") === "none") {
        $(el).css({ display: "table-row" });
      }
    });
  }
});

$("#close-trials-overlay").on("click", function () {
  $("#trials-overlay").css({ display: "none" });
  $("#trials-overlay iframe").attr(
    "src",
    ``
  );
});
