import { displaySuccess, displayInfo } from "./export.js";

$(document).ready(function () {
  //Copy link
  $(".rows .btn.btn-info").each(function (index, el) {
    $(el).on("click", function () {
      // Get the button text
      const buttonLink = $(this).data('link');
      const textInput = document.createElement("input");
      textInput.setAttribute("type", "text");
      textInput.setAttribute("value", buttonLink);
      textInput.setAttribute("hidden", true);
      textInput.select();
      textInput.setSelectionRange(0, 99999);
      const shareLink = textInput.value;
      navigator.clipboard.writeText(shareLink);
      displaySuccess("Link copied");
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
})
