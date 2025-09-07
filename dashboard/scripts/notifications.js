import { displayInfo } from "./export.js";

$(document).ready(function () {
  $("#notification-link").on("click", function () {
    if ($(this).find("span").css("display") === "block") {
      $(this).find("span").hide().text("");
      //Update status
      $.ajax({
        type: "POST",
        url: "../server/notification.php",
        dataType: "json",
        success: function (response) {
          for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
              const content = response.Info;
              if (content === "Status updated successfully") {
                $(".dropdown-menu dropdown-menu-lg dropdown-menu-right")
                  .empty()
                  .html(
                    `<span class='dropdown-item dropdown-header'>No new notifications</span>
                  <a href="./views/timeline.php" class="dropdown-item dropdown-footer">View all</a>
                `
                  );
                $(".badge badge-danger navbar-badge").text("");
              } else {
                displayInfo(content);
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

  // Toggle element visibility
  $("#zoomBox").on("click", function () {
    const $faIcon = $(this).find("i");
    const shouldExpand = $faIcon.hasClass("fa-expand");
    const $target = $("#timer-overlay");

    $target.css({
      width: shouldExpand ? "100vw" : "0vw",
      height: shouldExpand ? "100vh" : "0vh",
    });

    $faIcon
      .removeClass(shouldExpand ? "fa-expand" : "fa-compress")
      .addClass(shouldExpand ? "fa-compress" : "fa-expand");
  });

  const quizState = {
    timer: null,
    expiresAt: null,
    fetching: false,
  };

  const notificationSound = new Audio("../../assets/docs/wrong.mp3");

  function playSound() {
    notificationSound.currentTime = 0;
    notificationSound
      .play()
      .catch((err) => console.warn("Sound error:", err));
  }

  function formatTime(seconds) {
    const pad = (n) => String(n).padStart(2, "0");
    const hrs = pad(Math.floor(seconds / 3600));
    const mins = pad(Math.floor((seconds % 3600) / 60));
    const secs = pad(seconds % 60);
    return `${hrs}:${mins}:${secs}`;
  }

  function startQuizTimer(time) {
    const end = new Date(time);
    if (isNaN(end.getTime())) {
      $("#timer-overlay span").text("Invalid time received");
      return;
    }

    if (quizState.timer) clearInterval(quizState.timer);

    let seconds = Math.floor((end - new Date()) / 1000);
    quizState.timer = setInterval(() => {
      seconds--;
      $("#timer-overlay span").html(
        `Current question expires in: <br><b>${formatTime(Math.max(seconds, 0))}</b>`
      );

      if (seconds <= 0) {
        clearInterval(quizState.timer);
        playSound();
        $("#timer-overlay span").html(
          "Current question has expired."
        );
        setTimeout(() => {
            $("#timer-overlay span").html(
              "Starting next timer..."
            );
        }, 1200);
        setTimeout(() => { 
          startCountdownToSession(new Date(quizState.expiresAt)); 
        }, 3000);
      }
    }, 1000);
  }

  function startCountdownToSession(time) {
    const target = new Date(time);
    if (isNaN(target.getTime())) {
      $("#timer-overlay span").text("Invalid session start time");
      return;
    }

    if (quizState.timer) clearInterval(quizState.timer);

    quizState.timer = setInterval(() => {
      const now = new Date();
      const seconds = Math.floor((target - now) / 1000);

      if (seconds <= 0) {
        clearInterval(quizState.timer);
        $("#timer-overlay span").text(`Get ready...`);
        setTimeout(() => {
          fetchQuestionTimer();
        }, 2000);
      } else {
        $("#timer-overlay span").html(
          `Next session starts in: <br><b>${formatTime(seconds)}</b>`
        );
      }
    }, 1000);
  }

  async function fetchQuestionTimer() {
    if (quizState.fetching) return;
    quizState.fetching = true;

    try {
      const res = await $.get("../server/multiplayer.php");
      const { Info, data } = res;

      if (!data) {
        return displayInfo(Info || "Unable to fetch session");
      }

      if (Info === "Next session starts soon") {
        return startCountdownToSession(new Date(data.start));
      }

      if (
        [
          "The session has expired",
          "You have already played in this session",
        ].includes(Info)
      ) {
        return startCountdownToSession(new Date(data.next));
      } else {
        startQuizTimer(new Date(data.end));
        quizState.expiresAt = data.next;
      }
    } catch (err) {
      displayInfo("Server error: " + err.statusText);
    } finally {
      quizState.fetching = false;
    }
  }

  fetchQuestionTimer();
  
  // Navigation Buttom
  $("#timer-overlay button").on("click", function () {
    window.location.href = '../views/multiplayer.php';
  });
});