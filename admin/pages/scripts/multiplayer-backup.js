import { displaySuccess, displayInfo } from "../scripts/export.js";

document.addEventListener("DOMContentLoaded", function () {
  //Delete a question
  function finalizeGame(id, name) {
    const promptMessage = confirm(
      "Are you sure to finalize the game session: " + '" ' + name + ' "' + " ?"
    );
    if (promptMessage === true) {
      $.ajax({
        type: "POST",
        url: "../server/multiplayer.php",
        data: { id: id, name: name },
        dataType: "json",
        success: function (response) {
          for (var key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
              const content = response[key];
              if (content === "Game session finalized successfully") {
                $(".rows").each(function () {
                  if ($(this).attr("id") === id) {
                    $(this)
                      .find(".status button")
                      .removeClass("btn btn-danger")
                      .addClass("btn btn-success")
                      .text("Completed");
                    $(this)
                      .find(".action button")
                      .removeClass("btn btn-info")
                      .addClass("btn btn-success")
                      .html(
                        "<i class='fas fa-check' style='padding-right: 5px;'></i>  Finalized"
                      )
                      .attr("disabled", true)
                      .css({
                        background: "green",
                        width: "150px",
                      });
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

  $(".rows .btn.btn-info").each(function (index, el) {
    $(el).on("click", function () {
      const id = $(el).parent().parent().attr("id");
      const name = $(el).parent().parent().find(".name").text();
      finalizeGame(id, name);
    });
  });

  function getGames(count) {
    $("#loading-overlay").css({ display: "flex" });
    //get and update with number of questions selected by admin
    $.ajax({
      type: "GET",
      url: `../server/get-games.php?count=${count}`,
      dataType: "json",
      processData: false,
      contentType: false,
      cache: false,
      success: function (response) {
        for (const key in response) {
          if (Object.hasOwnProperty.call(response, key)) {
            const content = response.Info;
            if (content === "Games fetched") {
              //Notify admin
              $("#loading-overlay").css({ display: "none" });
              displaySuccess(`${count} games fetched successfully`);
              const games = response.games;
              $("tbody").empty();
              $.each(games, function (index, game) {
                //Get status
                let status = '';
                let action = '';

                if(game.status === 'Pending'){
                    status = "<button type='button' class='btn btn-danger btn-sm'>Pending</button>";
                    action = "<button type='button' class='btn btn-info btn-sm'>Finalize</button>";
                }
                else{
                    status = "<button type='button' class='btn btn-success btn-sm'>Completed</button>";
                    action = "<button type='button' class='btn btn-success btn-sm' style='background: green;width: 120px;'><i class='fas fa-check' style='padding-right: 5px;'></i>  Finalized</button>";
                }
                //Display sessions
                let list = `<tr class='rows' id='${game.id}'>
                              <td class='name'>${game.name}</td>
                              <td class='question'>${game.question}</td>
                              <td class='answer'>${game.answer}</td>
                              <td class='date'>${game.date}</td>
                              <td class='status'>${status}</td>
                              <td class='total'>${game.players}</td>
                              <td class='action'>${action}</td>
                            </tr>
                          `;
                $("tbody").append(list);
              });
            } else {
              //displayInfo(content);
              $("#loading-overlay").css({ display: "none" });
              displayInfo(content);
            }
          }
        }
        //Delete dynamically loaded questions
        $(".rows .btn.btn-info").each(function (index, el) {
          $(el).on("click", function () {
            const id = $(el).parent().parent().attr("id");
            const name = $(el).parent().parent().find(".name").text();
            finalizeGame(id, name);
          });
        });
      },
      error: function (e) {
        displayInfo("Error connecting to server");
      },
    });
  }

  //Filter number of questions on the page
  $("#filter").on("change", function () {
    const count = $(this).val();
    getGames(count);
  });
  
    //Refresh page
  $("#refresh").on("click", function () {
    window.location.reload();
  });

  //Indent all inner child navs
  $(".nav-sidebar").addClass("nav-child-indent");

  //Search function
  $("#page-search").on("keyup", function () {
    let searchValue = $(this).val();
    if (searchValue !== "") {
      $(".rows").each(function (index, el) {
        if ($(el).find("td").text().toLowerCase().includes(searchValue)) {
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
});
