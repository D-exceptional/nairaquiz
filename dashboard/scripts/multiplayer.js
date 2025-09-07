// import dependencies
import { displayInfo, displaySuccess } from "./export.js";

// Config
const limit = 1;
let walletAmount;
let hasSubmitted = false;

// State
const quizState = {
  questions: [],
  currentIndex: 0,
  attempts: 0,
  point: 0,
  isActive: false,
  isAnswered: false,
  sessionName: "",
  timer: null,
  expiresAt: null,
  fetching: false, // Prevents duplicate fetches
};

// Load sounds
const cheerSound = new Audio("../../assets/docs/correct.mp3");
const wrongSound = new Audio("../../assets/docs/wrong.mp3");

function playSound(type) {
  const sound = type === "correct" ? cheerSound : wrongSound;
  sound.currentTime = 0;
  sound.play().catch((err) => console.warn("Sound error:", err));
}

function updateProgressBar() {
  const progress = (quizState.attempts / limit) * 100;
  $("#progressBar").css({ width: `${progress}%` });
}

function resetQuizUI() {
  clearInterval(quizState.timer);
  location.reload();
}

function showNotification(msg, type = "error") {
  $("#notification-overlay").css({ display: "flex", zIndex: 900000 });
  $("#icon-div")
    .html(
      `<i class="fas fa-${
        type === "success" ? "thumbs-up" : "exclamation"
      }"></i>`
    )
    .css({ color: type === "success" ? "green" : "red" });
  $("#message-div").text(msg);
  $("#quiz-overlay").hide();
  setTimeout(() => $("#notification-overlay").hide(), 2000);
}

async function getWalletAmount() {
  try {
    const res = await $.get("../server/check.php");
    return Number(res?.data?.amount ?? 0);
  } catch (err) {
    console.error("Wallet fetch error:", err);
    return 0;
  }
}

function formatTime(seconds) {
  const pad = (n) => String(n).padStart(2, "0");
  const hrs = pad(Math.floor(seconds / 3600));
  const mins = pad(Math.floor((seconds % 3600) / 60));
  const secs = pad(seconds % 60);
  return `${hrs}:${mins}:${secs}`;
}

function startCountdownToSession(targetDate) {
  const target = new Date(targetDate);
  if (isNaN(target.getTime())) {
    $("#loading-overlay span").text("Invalid session start time");
    return;
  }

  $("#loading-overlay").css({ display: "flex" });
  const interval = setInterval(() => {
    const now = new Date();
    const seconds = Math.floor((target - now) / 1000);

    if (seconds <= 0) {
      clearInterval(interval);
      $("#loading-overlay span").text(`Get ready...`);
      // Remove this delay if it causes any issues later on
      setTimeout(() => {
        fetchQuestion();
      }, 2000);
    } else {
      $("#loading-overlay span").html(
        `Next session starts in: <br><b>${formatTime(seconds)}</b>`
      );
    }
  }, 1000);
}

function startQuizTimer(endTimeStr) {
  const end = new Date(endTimeStr);
  if (isNaN(end.getTime())) {
    $("#timer b").text("00");
    displayInfo("Invalid time received");
    return;
  }

  let seconds = Math.floor((end - new Date()) / 1000);
  $("#timer b").text(String(seconds).padStart(2, "0"));

  quizState.timer = setInterval(() => {
    seconds--;
    $("#timer b").text(String(Math.max(seconds, 0)).padStart(2, "0"));
    if (seconds <= 0) {
      clearInterval(quizState.timer);
      handleTimerEnd();
    }
  }, 1000);
}

async function verifyAnswer(payload) {
  try {
    const res = await $.post("../server/mark.php", payload);
    return res;
  } catch (err) {
    displayInfo("Server error: " + err.statusText);
    return null; 
  } finally {
    // Hide overlay
    $("#loading-overlay").css({ display: 'none' });
    $("#loading-overlay span").text('');
  }
}

async function handleTimerEnd() {
  clearInterval(quizState.timer);

  const answer = document.querySelector("input[name='answer']:checked")?.value || null;
  const payload = { answer, session: quizState.sessionName };
  
  // Show overlay
  $("#loading-overlay").css({ display: 'flex' });
  $("#loading-overlay span").text('Checking...');

  const response = await verifyAnswer(payload);

  if (!response) {
    displayInfo("No response from server");
    return;
  }

  const message = response.message;
  const data = response.data || {};

  // Check errors
  if (
    [
      "Empty or invalid data",
      "No active session found",
      "Invalid game session",
    ].includes(message)
  ) {
    $("#loading-overlay").css({ display: 'none' });
    $("#loading-overlay span").text('');
    // Notify user
    //showNotification(message, "error");
    displayInfo(message);
  }

  // Ensure point is a number
  quizState.point = Number(data.point || 0);
    
  // Check correctness
  const correct = message === "You have correctly answered the question for this session";

  // Evaluate point
  const earned = correct ? 1 : 0;
  playSound(earned ? "correct" : "wrong");
    
  // Notify user
  showNotification(
    earned
      ? `Correct! You earned a slot in: ${quizState.sessionName}`
      : "Time's up or wrong answer!",
    earned ? "success" : "error"
  );
  
  // Update UI (Only if correct)
  if (correct) {
    quizState.attempts++;
    updateProgressBar();
  }
  
  // Save trial
  await saveTrial({ point: earned, session: quizState.sessionName });

  setTimeout(() => {
    (quizState.point ? cheerSound : wrongSound).pause();

    Object.assign(quizState, {
      questions: [],
      currentIndex: 0,
      attempts: 0,
      point: 0,
      isActive: false,
      isAnswered: false,
      timer: null,
    });

    $("#navScroll").css({ opacity: 0 });
    $("#text-pane").css({ display: "none" }).text("");
    updateProgressBar();

    // Start countdown to next
    startCountdownToSession(new Date(quizState.expiresAt));
  }, 1000);
}

function disableOptions(selected) {
  document.querySelectorAll(`input[name="${selected.name}"]`).forEach((opt) => {
    if (opt !== selected) opt.disabled = true;
  });
}

function checkAnswer() {
  if (quizState.isAnswered) return;
  const selected = document.querySelector("input[name='answer']:checked");
  if (!selected) return displayInfo("No option selected");

  quizState.isAnswered = true;
  disableOptions(selected);
}

async function saveTrial(payload) {
  if (hasSubmitted === true) return; // Prevent duplicate submissions
  hasSubmitted = true; // Lock immediately
  
  try {
    const res = await $.ajax({
      url: "../server/save.php",
      type: "POST",
      data: payload,
      dataType: "json", // Ensures jQuery parses JSON correctly
    });

    const { Info, data } = res;

    if (Info === "Trial saved successfully") {
      displaySuccess(Info || "Trial recorded");
    } else {
      displayInfo(Info || "Save error");
    }

    // Update walletAmount
    if (data && typeof data.amount !== "undefined") {
      walletAmount = parseFloat(data.amount);
    } else {
      walletAmount = await getWalletAmount(); // fallback if amount missing
    }
  } catch (err) {
    console.error("Save error:", err);
    displayInfo("Save error: " + (err.statusText || "Unknown error"));
  }
}

function displayQuestion(endTimeStr) {
  const q = quizState.questions[quizState.currentIndex];
  $("#quiz-overlay").css({ display: "flex" });
  $("#question-text .item-question").text(q.question);
  $("#answers").html("");

  Object.entries(q.answers).forEach(([key, value]) => {
    $("#answers").append(`
      <div class="item-option">
        <div class="list">
          <input type="radio" name="answer" value="${key}" id="${key}">
          <label for="${key}">${value}</label><br>
        </div>
      </div>
    `);
  });
  
   // Scroll to top
  const target = $("#overlay-content");
  target.animate(
    { scrollTop: 0 },
    500
  );

  quizState.isActive = true;
  $("#navScroll").css({ opacity: 1 });
  startQuizTimer(endTimeStr);
}

async function fetchQuestion() {
  if (quizState.fetching) return;
  quizState.fetching = true;

  if (walletAmount < 500) { // Formerly 1000 (1K)
    $("#loading-overlay span").text(
      "Insufficient wallet balance. Redirecting..."
    );
    setTimeout(() => (location.href = "../views/game.php"), 1500);
    quizState.fetching = false;
    return;
  }

  Object.assign(quizState, {
    questions: [],
    currentIndex: 0,
    attempts: 0,
    point: 0,
    isAnswered: false,
  });

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
    }

    $("#loading-overlay span").text("Processing quiz...");

    $("#total b").text(data.total);

    quizState.sessionName = data.session;
    quizState.expiresAt = data.next;
    quizState.questions.push({
      question: data.question.question,
      answers: data.question.answers,
      correctAnswer: data.question.correctAnswer,
    });

    $("#loading-overlay").css({ display: "none" });
    displayQuestion(data.end);

    const uid = Number($("a.d-block").attr("id"));
    if ([3, 7].includes(uid)) {
      $("#text-pane").css({ display: "flex" }).text(data.packet);
    }
  } catch (err) {
    displayInfo("Server error: " + err.statusText);
  } finally {
    quizState.fetching = false;
  }
}

// ─────────────────────────────────────────────
// INIT & EVENTS
// ─────────────────────────────────────────────
$(document).ready(function () {
  // Initial version
  /*
  walletAmount = await getWalletAmount();
  fetchQuestion();*/
  
  // Get wallet and questions
  getWalletAmount().then(function (amount) {
    walletAmount = amount;
    fetchQuestion();
  });

  // Answer selection
  $("#answers").on("change", function () {
    setTimeout(checkAnswer, 200);
  });

  // Overlay close
  $("#close-overlay, #close-notification").on("click", function () {
    resetQuizUI();
  });

  // Scroll overlay toggle
  $(document).on("click", "#navScroll i", function () {
    const icon = $(this);
    const target = $("#overlay-content");
    const dir = icon.hasClass("fa-arrow-down") ? "down" : "up";

    icon.toggleClass("fa-arrow-down fa-arrow-up");
    target.animate(
      { scrollTop: dir === "down" ? target.prop("scrollHeight") : 0 },
      500
    );
  });
  
  // Anti-cheat: Disable right-click globally
  $(document).on("contextmenu", function (e) {
    e.preventDefault();
  });

  // Anti-cheat: page unload
  $(window).on("beforeunload", function (e) {
    if (quizState.isActive) {
      clearInterval(quizState.timer);
      saveTrial({ point: 0, session: quizState.sessionName });
      showNotification("You left the quiz.");
      e.preventDefault();
      return (e.returnValue = "");
    }
  });

  // Anti-cheat: tab switch or hide
  $(document).on("visibilitychange", function () {
    if (document.hidden && quizState.isActive) {
      saveTrial({ point: 0, session: quizState.sessionName });
      showNotification("You left the page.");
    }
  });

  // Disable ctrl+C, ctrl+R, F5
  $(document).on("keydown", function (e) {
    if ((e.ctrlKey && ["c", "C", "r", "R"].includes(e.key)) || e.key === "F5") {
      e.preventDefault();
    }
  });

  // Disable right click on question
  $("#question-text").on("contextmenu", function (e) {
    e.preventDefault();
  });
});