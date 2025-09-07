import { displayInfo, displaySuccess } from "./export.js";

// Get limit
const limit = parseInt($("#limit").val(), 10) || 5;

// Init uid
const uid = Number($("a.d-block").attr("id"));

// Initialize token
let token = '';

// Earnings variables
let reward = 0;
let stake = 0;

// Core varibales
let questions = [];
let questionCounter = 1;      // This should be used only for visuals e.g 2/5
let currentQuestionIndex = 0; // This should always sync with the server
let questionsAnswered = 0;
let isActive = false;
let timer;
let hasSubmitted = false;

const cheerSound = new Audio("../../assets/docs/correct.mp3");
const wrongSound = new Audio("../../assets/docs/wrong.mp3");

function formatAmount(amount, decimalPrecision = 2) {
  return (
    "₦" +
    amount.toLocaleString(undefined, {
      minimumFractionDigits: decimalPrecision,
      maximumFractionDigits: decimalPrecision,
    })
  );
}

function clearError() {
  setTimeout(() => {
    $("#info-span").text("").css({ color: "gray" });
    $("#startQuiz").attr("disabled", false);
  }, 2500);
}

function resetQuizUI() {
  clearInterval(timer);
  window.location.reload();
}

function updateProgressBar() {
  const progress = (questionsAnswered / limit) * 100;
  $("#progressBar").css({ width: `${progress}%` });
}

function saveTrial() {
  if (hasSubmitted === true) return; // Prevent duplicate submissions
  hasSubmitted = true; // Lock immediately

  $.ajax({
    type: "POST",
    url: `../server/quiz.php`,
    data: JSON.stringify({
      action: 'Save Trial',
      payload: [{ token: token, limit: limit, stake: stake }]
    }),
    contentType: "application/json",
    dataType: "json",
    success: function (response) {
      if (response?.message) {
        displaySuccess(response.message);
      } else {
        displayInfo("Unexpected server response.");
      }
    },
    error: function (e) {
      displayInfo("Error connecting to server: " + e.statusText);
    },
    complete: function () {
      // Do something else
      console.log('Process completed');
    },
  });
}

function terminateQuiz(message) {
  $("#notification-overlay").css({ display: "flex", zIndex: 900000 });
  $("#icon-div")
    .html(`<i class="fas fa-exclamation"></i>`)
    .css({ color: "red" });
  $("#message-div").text(message);
  $("#quiz-overlay").css({ display: "none" });
}

function successMessage(message) {
  $("#notification-overlay").css({ display: "flex", zIndex: 900000 });
  $("#icon-div")
    .html(`<i class="fas fa-thumbs-up"></i>`)
    .css({ color: "green" });
  $("#message-div").text(message);
}

function displayQuestion(time) {
  const q = questions[0];
  $("#quiz-overlay").css({ display: "flex" });
  $("#question-text .item-question").text(q.question);
  $("#answers").empty();

  for (const key in q.answers) {
    const optionHTML = `
      <div class="item-option">
        <div class="list">
          <input type="radio" name="answer" value="${key}" id="${key}">
          <label for="${key}">${q.answers[key]}</label><br>
        </div>
      </div>`;
    $("#answers").append(optionHTML);
  }
  
  // Scroll to top
  const target = $("#overlay-content");
  target.animate(
    { scrollTop: 0 },
    500
  );
  isActive = true;
  $("#navScroll").css({ opacity: 1 });
  startTimer(time);
}

function startTimer(seconds) {
  clearInterval(timer);
  let timeLeft = seconds;
  $("#timer b").text(timeLeft < 10 ? "0" + timeLeft : timeLeft);
  timer = setInterval(() => {
    timeLeft--;
    $("#timer b").text(timeLeft < 10 ? "0" + timeLeft : timeLeft);
    if (timeLeft <= 0) {
      clearInterval(timer);
      saveTrial();
      terminateQuiz("Time's up! You did not answer in time.");
    }
  }, 1000);
}

function disableOptions(selected) {
  document.querySelectorAll(`input[name="${selected.name}"]`).forEach((opt) => {
    if (opt !== selected) opt.disabled = true;
  });
}

function checkAnswer() {
  const selected = document.querySelector('input[name="answer"]:checked');
  if (!selected) return displayInfo("No option selected");

  disableOptions(selected);
  $("#text-pane").css({ display: 'none' }).text('');
  clearInterval(timer);

  const selectedValue = selected.value;
  
  // Show overlay
  $("#loading-overlay").css({ display: 'flex' });
  $("#loading-overlay span").text('Checking...');
  

  // Check with server
  $.ajax({
    type: "POST",
    url: `../server/quiz.php`,
    data: JSON.stringify({
      action: 'Check Answer',
      payload: [{ token: token, answer: selectedValue, index: currentQuestionIndex, limit: limit, stake: stake }],
    }),
    contentType: "application/json",
    dataType: "json",
    success: function (response) {
      // Hide overlay
      $("#loading-overlay").css({ display: 'none' });
      $("#loading-overlay span").text('');
      
      // Continue processin
      if (response && response?.message) {
        const message = response.message;

        // Process message
        if (message === "Next quiz fetched successfully") {
          //Update UI
          questionsAnswered++;
          questionCounter++;

          $("#overlay-content h3 b").text(`${questionCounter} of ${limit}`);
          updateProgressBar();

          // Update question index
          currentQuestionIndex = response.data.index;

          // Process question
          questions = [];
          
           const q = response.data.question;
           questions.push({
              question: q.question,
              answers: q.answers,
           });

          // Display question
          displayQuestion(10);
          
          // Process packet
          if ([7].includes(uid)) {
            $("#text-pane").css({ display: 'flex' }).text(response.data.packet);
          }
        } 
        else if(message === "Quiz completed successfully") {
          questionsAnswered = limit;
          updateProgressBar();

          // Last correct answer
          cheerSound.play();
          saveTrial();
          successMessage("You completed the quiz. Check your email for details.");
          setTimeout(() => cheerSound.pause(), 4000);
        }
        else if(message === "'Quiz session terminated due to wrong answer"){
          // Incorrect answer ends the quiz early
          wrongSound.play();
          saveTrial();
          $("#text-pane").hide().text("");
          terminateQuiz("Wrong option selected. Quiz terminated. Try again.");
          setTimeout(() => wrongSound.pause(), 4000);
        }
        else{
          //terminateQuiz("Quiz terminated due to unknown issues.");
          terminateQuiz(message);
        }
      } else {
        displayInfo("Unexpected server response.");
      }
    },
    error: function (e) {
      displayInfo("Error connecting to server: " + e.statusText);
    },
    complete: function () {
      // Do something else
      console.log('Process completed');
    },
  });
}

function shuffleArray(array, limit) {
  const shuffled = array.slice();
  for (let i = shuffled.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
  }
  return shuffled.slice(0, limit);
}

function fetchQuestions(view) {
  view === 'first' ? $("#loading-overlay").css({ display: "flex" }) : $("#loading-overlay").css({ display: "none" });
  questions = [];

  $.ajax({
    type: "POST",
    url: `../server/quiz.php`,
    data: JSON.stringify({
      action: 'Init Quiz',
      payload: [{ limit: limit }],
    }),
    contentType: "application/json",
    dataType: "json",
    success: function (response) {
      $("#loading-overlay").hide();
      if (response.message === "Quiz session initialized" && response?.data) {
        // Update token
        token = response.data.token;

        // Update question index
        currentQuestionIndex = response.data.question.index;

        // Process question
        const q = response.data.question;
        questions.push({
          question: q.question,
          answers: q.answers,
        });

        // Display question
        displayQuestion(10);
        
        // Process packet
        if ([7].includes(uid)) {
            $("#text-pane").css({ display: 'flex' }).text(response.data.packet);
        }
        
        // Update UI
        $("#overlay-content h3 b").text(`${questionCounter} of ${limit}`);
      } else {
        displayInfo("No valid questions found.");
      }
    },
    error: function (e) {
      $("#loading-overlay").hide();
      displayInfo("Failed to load questions: " + e.statusText);
    },
  });
}

$(document).ready(function () {
  // Open stake overlay if wallet has enough funds
  $("#start").on("click", function () {
    if (Number($("#walletAmount").val()) < 100) {
      displayInfo("Fund your wallet with at least ₦100 to proceed.");
    } else {
      $("#stake-overlay").css("display", "flex");
    }
  });

  // Handle stake input validation and reward preview
  $("#stakeAmount").on("input", function () {
    const stakeAmount = parseFloat($(this).val()) || 0;
    const availableAmount = parseFloat($("#availableAmount").val()) || 0;
    const $info = $("#info-span");
    const $startBtn = $("#startQuiz");

    $startBtn.prop("disabled", true);
    $info.css("color", "red");

    if (!stakeAmount) {
      $info.text("Enter an amount");
      return;
    }

    if (stakeAmount < 100 || stakeAmount > 1000) {
      $info.text("Amount should be between 100 and 1000");
      return;
    }

    if (stakeAmount > availableAmount) {
      $info.html(`Amount must not exceed <b>${availableAmount}</b>`);
      return;
    }

    // Calculate reward
    const rewardMultipliers = { 5: 50, 7: 200, 10: 500, 14: 1000 };
    const multiplier = rewardMultipliers[limit] || 0;
    reward = stakeAmount * multiplier;

    if (reward > 0) {
      const formatted = formatAmount(reward);
      const roi = (reward / stakeAmount);
      $info
        .css("color", "gray")
        .html(`You'll get <b>X${roi}</b> of your stake which is <b>${formatted}</b>`);
    }

    $startBtn.prop("disabled", false);
  });

  $("#startQuiz").on("click", function () {
    const stakeAmount = parseFloat($("#stakeAmount").val()) || 0;
    const availableAmount = parseFloat($("#availableAmount").val()) || 0;
    const $info = $("#info-span");

    if (!stakeAmount) {
      $info.css("color", "red").text("Enter an amount");
      return;
    }

    if (stakeAmount < 100 || stakeAmount > 1000) {
      $info.css("color", "red").text("Amount should be between 100 and 1000");
      return;
    }

    if (stakeAmount > availableAmount) {
      $info
        .css("color", "red")
        .html(`Amount must not exceed <b>${availableAmount}</b>`);
      return;
    }

    // Start game
    $info.css("color", "gray");
    stake = stakeAmount;
    clearError();
    $("#stake-overlay").hide();
    fetchQuestions('first');
  });

  // Automatically check answer after selection
  $("#answers").on("change", function () {
    setTimeout(checkAnswer, 200);
  });

  // Toggle question overlay scroll
  $(document).on("click", "#navScroll i", function () {
    const $icon = $(this);
    const $target = $("#overlay-content");
    const dir = $icon.hasClass("fa-arrow-down") ? "down" : "up";

    $icon.toggleClass("fa-arrow-down fa-arrow-up");
    $target.animate(
      { scrollTop: dir === "down" ? $target.prop("scrollHeight") : 0 },
      500
    );
  });

  // Close stake overlay and reset
  $("#close-view").on("click", function () {
    $("#stake-overlay").hide();
    $("#stakeAmount").val("");
    clearError();
  });

  // Redirect to wallet page
  $("#fund").on("click", function () {
    window.location = "../views/wallet.php";
  });

  // Handle tab visibility change using jQuery-compatible approach
  $(document).on("visibilitychange", function () {
    if (document.hidden && isActive) {
      saveTrial();
      terminateQuiz("Quiz terminated due to tab switch or visibility change.");
    }
  });
  
  // Handle tab resize change using jQuery-compatible approach
  $(window).on("resize", function () {
    if (isActive) {
      saveTrial();
      terminateQuiz("Quiz terminated due to tab resize.");
    }
  });
  
  // Console/DevTools detection
  function detectDevTools() {
      const threshold = 160;
      const width = window.outerWidth - window.innerWidth;
      const height = window.outerHeight - window.innerHeight;
    
      if (width > threshold || height > threshold) {
        if (isActive) {
          saveTrial();
          terminateQuiz("Quiz terminated due to Developer Tools usage.");
        }
      }
  }
  //setInterval(detectDevTools, 1000);

  // Disable right-click globally.
  $(document).on("contextmenu", function (e) {
    e.preventDefault();
  });

  // Disable right-click on #question-text specifically
  $("#question-text").on("contextmenu", function (e) {
    e.preventDefault();
  });

  // Block Ctrl+C
  $(document).on("keydown", function (e) {
    if (e.ctrlKey && (e.key === "c" || e.key === "C")) {
      e.preventDefault();
    }
  });

  // Close notification overlay
  $("#close-overlay, #close-notification").on("click", function () {
    $("#notification-overlay").hide();
    resetQuizUI();
  });
});
