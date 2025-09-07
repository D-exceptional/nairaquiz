import { displaySuccess, displayInfo } from "../scripts/export.js";

document.addEventListener("DOMContentLoaded", function () {

  // ───────────────────────────────────────────────
  // Finalize a game session
  // ───────────────────────────────────────────────
  function finalizeGame(id, name) {
    const confirmMessage = confirm(`Are you sure to finalize the game session: "${name}"?`);

    if (!confirmMessage) return;

    $.ajax({
      type: "POST",
      url: "../server/multiplayer.php",
      data: { id, name },
      dataType: "json",
      success: function (response) {
        const content = response?.Info || "Unknown response";

        if (content === "Game session finalized successfully") {
          const row = $(`.rows#${id}`);

          row.find(".status button")
            .removeClass("btn-danger")
            .addClass("btn-success")
            .text("Completed");

          row.find(".action button")
            .removeClass("btn-info")
            .addClass("btn-success")
            .html("<i class='fas fa-check' style='padding-right: 5px;'></i> Finalized")
            .attr("disabled", true)
            .css({ background: "green", width: "150px" });
            
            // Remove from UI
            setTimeout(() => { row.remove(); }, 2000);
            
          displaySuccess(content);
        } else {
          const row = $(`.rows#${id}`);
          row.find(".action button").text("Finalize");
          displayInfo(content);
        }
      },
      error: function (err) {
        const row = $(`.rows#${id}`);
        row.find(".action button").text("Finalize");
        const msg = err.responseText || "An unexpected error occurred.";
        console.error("Finalize error:", msg);
        displayInfo("Server error: " + msg);
      }
    });
  }

  // ───────────────────────────────────────────────
  // Load game sessions from server
  // ───────────────────────────────────────────────
  function getGames(count) {
    $("#loading-overlay").show();

    $.ajax({
      type: "GET",
      url: `../server/get-games.php?count=${count}`,
      dataType: "json",
      cache: false,
      success: function (response) {
        const content = response?.Info || "Unknown";

        if (content === "Games fetched") {
          displaySuccess(`${count} games fetched successfully`);
          $("#loading-overlay").hide();
          renderGames(response.games);
        } else {
          displayInfo(content);
          $("#loading-overlay").hide();
        }
      },
      error: function (err) {
        console.error("Fetch error:", err);
        displayInfo("Error connecting to server");
        $("#loading-overlay").hide();
      }
    });
  }

  // ───────────────────────────────────────────────
  // Render game rows in the table
  // ───────────────────────────────────────────────
  function renderGames(games) {
    const tbody = $("tbody");
    tbody.empty();

    games.forEach((game) => {
      const statusBtn = game.status === "Pending"
        ? "<button type='button' class='btn btn-danger btn-sm'>Pending</button>"
        : "<button type='button' class='btn btn-success btn-sm'>Completed</button>";

      const actionBtn = game.status === "Pending"
        ? "<button type='button' class='btn btn-info btn-sm'>Finalize</button>"
        : "<button type='button' class='btn btn-success btn-sm' disabled style='background: green; width: 120px;'><i class='fas fa-check' style='padding-right: 5px;'></i> Finalized</button>";

      const rowHTML = `
        <tr class='rows' id='${game.id}'>
          <td class='name'>${game.name}</td>
          <td class='question'>${game.question}</td>
          <td class='answer'>${game.answer}</td>
          <td class='date'>${game.date}</td>
          <td class='status'>${statusBtn}</td>
          <td class='total'>${game.players}</td>
          <td class='total'>${game.amount}</td>
          <td class='action'>${actionBtn}</td>
        </tr>
      `;

      tbody.append(rowHTML);
    });

    attachFinalizeHandlers(); // Reattach handlers after dynamic DOM update
  }

  // ───────────────────────────────────────────────
  // Attach click handlers to dynamic finalize buttons
  // ───────────────────────────────────────────────
  function attachFinalizeHandlers() {
    $(".rows .btn.btn-info").off("click").on("click", function () {
      const row = $(this).closest(".rows");
      const id = row.attr("id");
      const name = row.find(".name").text().trim();
      $(this).text("Finalizing...");
      finalizeGame(id, name);
    });
  }

  // ───────────────────────────────────────────────
  // Search filter for table rows
  // ───────────────────────────────────────────────
  $("#page-search").on("keyup", function () {
    const searchValue = $(this).val().toLowerCase();

    $(".rows").each(function () {
      const row = $(this);
      const text = row.find("td").text().toLowerCase();
      row.toggle(text.includes(searchValue));
    });
  });

  // ───────────────────────────────────────────────
  // UI Event Listeners
  // ───────────────────────────────────────────────
  $("#filter").on("change", function () {
    const count = $(this).val();
    if (count) getGames(count);
  });

  $("#refresh").on("click", function () {
    window.location.reload();
  });

  $(".nav-sidebar").addClass("nav-child-indent");

  // ───────────────────────────────────────────────
  // Initial Handler Binding
  // ───────────────────────────────────────────────
  attachFinalizeHandlers(); // Bind handlers on page load
});
