// Get the URL and parse query parameters
const queryString = new URL(window.location);
const urlParams = new URLSearchParams(queryString.search);
const limit = parseInt(urlParams.get("count"), 10) || 10; // Default to 10 if not specified
let isActive = false;

// Load sounds
const cheerSound = new Audio();
    cheerSound.src = "../assets/docs/correct.mp3";
const wrongSound = new Audio();
    wrongSound.src = "../assets/docs/wrong.mp3";

/**** Demo Questions ****/
const myQuestions = [
  {
    question: "What is the capital of France?",
    answers: {
      a: "Berlin",
      b: "Madrid",
      c: "Paris",
      d: "Rome",
    },
    correctAnswer: "c",
  },
  {
    question: "What is 2 + 2?",
    answers: {
      a: "3",
      b: "4",
      c: "5",
      d: "6",
    },
    correctAnswer: "b",
  },
  {
    question: "Which planet is known as the Red Planet?",
    answers: {
      a: "Earth",
      b: "Mars",
      c: "Jupiter",
      d: "Saturn",
    },
    correctAnswer: "b",
  },
  {
    question: "Who wrote 'Romeo and Juliet'?",
    answers: {
      a: "Mark Twain",
      b: "Charles Dickens",
      c: "William Shakespeare",
      d: "Jane Austen",
    },
    correctAnswer: "c",
  },
  {
    question: "What is the chemical symbol for water?",
    answers: {
      a: "H2O",
      b: "CO2",
      c: "O2",
      d: "NaCl",
    },
    correctAnswer: "a",
  },
  {
    question: "Which organ pumps blood in the human body?",
    answers: {
      a: "Liver",
      b: "Brain",
      c: "Heart",
      d: "Lungs",
    },
    correctAnswer: "c",
  },
  {
    question: "What is the largest ocean on Earth?",
    answers: {
      a: "Atlantic Ocean",
      b: "Indian Ocean",
      c: "Arctic Ocean",
      d: "Pacific Ocean",
    },
    correctAnswer: "d",
  },
  {
    question: "Who painted the Mona Lisa?",
    answers: {
      a: "Vincent van Gogh",
      b: "Pablo Picasso",
      c: "Leonardo da Vinci",
      d: "Claude Monet",
    },
    correctAnswer: "c",
  },
  {
    question: "What is the smallest prime number?",
    answers: {
      a: "0",
      b: "1",
      c: "2",
      d: "3",
    },
    correctAnswer: "c",
  },
  {
    question: "What is the main ingredient in guacamole?",
    answers: {
      a: "Tomato",
      b: "Avocado",
      c: "Pepper",
      d: "Onion",
    },
    correctAnswer: "b",
  },
  {
    question: "In which year did the Titanic sink?",
    answers: {
      a: "1912",
      b: "1914",
      c: "1916",
      d: "1918",
    },
    correctAnswer: "a",
  },
  {
    question: "Which gas do plants absorb from the atmosphere?",
    answers: {
      a: "Oxygen",
      b: "Carbon Dioxide",
      c: "Nitrogen",
      d: "Hydrogen",
    },
    correctAnswer: "b",
  },
  {
    question: "What is the largest mammal in the world?",
    answers: {
      a: "Elephant",
      b: "Blue Whale",
      c: "Giraffe",
      d: "Great White Shark",
    },
    correctAnswer: "b",
  },
  {
    question: "What is the currency of Japan?",
    answers: {
      a: "Yen",
      b: "Won",
      c: "Dollar",
      d: "Pound",
    },
    correctAnswer: "a",
  },
  {
    question: "Which element has the atomic number 1?",
    answers: {
      a: "Helium",
      b: "Hydrogen",
      c: "Oxygen",
      d: "Lithium",
    },
    correctAnswer: "b",
  },
  {
    question: "What is the freezing point of water?",
    answers: {
      a: "0°C",
      b: "32°F",
      c: "100°C",
      d: "212°F",
    },
    correctAnswer: "a",
  },
  {
    question: "Which continent is known as the 'Dark Continent'?",
    answers: {
      a: "Asia",
      b: "Africa",
      c: "Europe",
      d: "Australia",
    },
    correctAnswer: "b",
  },
  {
    question: "What is the speed of light?",
    answers: {
      a: "300,000 km/s",
      b: "150,000 km/s",
      c: "600,000 km/s",
      d: "1,000,000 km/s",
    },
    correctAnswer: "a",
  },
  {
    question: "Which country is known for the Great Wall?",
    answers: {
      a: "Japan",
      b: "India",
      c: "China",
      d: "Brazil",
    },
    correctAnswer: "c",
  },
  {
    question: "What is the hardest natural substance on Earth?",
    answers: {
      a: "Gold",
      b: "Diamond",
      c: "Iron",
      d: "Quartz",
    },
    correctAnswer: "b",
  },
  {
    question: "What is the longest river in the world?",
    answers: {
      a: "Amazon",
      b: "Nile",
      c: "Yangtze",
      d: "Mississippi",
    },
    correctAnswer: "b",
  },
  {
    question: "What is the main language spoken in Brazil?",
    answers: {
      a: "Spanish",
      b: "Portuguese",
      c: "French",
      d: "English",
    },
    correctAnswer: "b",
  },
  {
    question: "Who was the first President of the United States?",
    answers: {
      a: "George Washington",
      b: "Thomas Jefferson",
      c: "Abraham Lincoln",
      d: "John Adams",
    },
    correctAnswer: "a",
  },
  {
    question: "What is the capital of Australia?",
    answers: {
      a: "Sydney",
      b: "Melbourne",
      c: "Canberra",
      d: "Brisbane",
    },
    correctAnswer: "c",
  },
  {
    question: "What is the largest desert in the world?",
    answers: {
      a: "Sahara",
      b: "Arabian",
      c: "Gobi",
      d: "Antarctic",
    },
    correctAnswer: "d",
  },
  {
    question: "In which year did World War II end?",
    answers: {
      a: "1942",
      b: "1945",
      c: "1947",
      d: "1950",
    },
    correctAnswer: "b",
  },
  {
    question: "What is the main ingredient in bread?",
    answers: {
      a: "Sugar",
      b: "Flour",
      c: "Salt",
      d: "Water",
    },
    correctAnswer: "b",
  },
  {
    question: "What is the square root of 16?",
    answers: {
      a: "2",
      b: "4",
      c: "8",
      d: "16",
    },
    correctAnswer: "b",
  },
  {
    question: "What is the largest country in the world?",
    answers: {
      a: "China",
      b: "United States",
      c: "Russia",
      d: "Canada",
    },
    correctAnswer: "c",
  },
  {
    question: "Which element is represented by the symbol 'O'?",
    answers: {
      a: "Osmium",
      b: "Oxygen",
      c: "Gold",
      d: "Silver",
    },
    correctAnswer: "b",
  },
  {
    question: "What is the capital of Canada?",
    answers: {
      a: "Toronto",
      b: "Ottawa",
      c: "Vancouver",
      d: "Montreal",
    },
    correctAnswer: "b",
  },
  {
    question: "Which fruit is known as the king of fruits?",
    answers: {
      a: "Mango",
      b: "Banana",
      c: "Durian",
      d: "Pineapple",
    },
    correctAnswer: "c",
  },
  {
    question: "What is the hardest rock?",
    answers: {
      a: "Basalt",
      b: "Granite",
      c: "Diamond",
      d: "Slate",
    },
    correctAnswer: "c",
  },
  {
    question: "Who discovered penicillin?",
    answers: {
      a: "Louis Pasteur",
      b: "Alexander Fleming",
      c: "Marie Curie",
      d: "Isaac Newton",
    },
    correctAnswer: "b",
  },
  {
    question: "What is the main gas found in the air we breathe?",
    answers: {
      a: "Oxygen",
      b: "Carbon Dioxide",
      c: "Nitrogen",
      d: "Argon",
    },
    correctAnswer: "c",
  },
  {
    question: "What is the boiling point of water?",
    answers: {
      a: "0°C",
      b: "50°C",
      c: "100°C",
      d: "212°C",
    },
    correctAnswer: "c",
  },
  {
    question: "Who was known as the 'Iron Lady'?",
    answers: {
      a: "Margaret Thatcher",
      b: "Angela Merkel",
      c: "Indira Gandhi",
      d: "Golda Meir",
    },
    correctAnswer: "a",
  },
  {
    question: "What is the largest planet in our solar system?",
    answers: {
      a: "Earth",
      b: "Mars",
      c: "Jupiter",
      d: "Saturn",
    },
    correctAnswer: "c",
  },
  {
    question: "Which animal is known as the 'King of the Jungle'?",
    answers: {
      a: "Tiger",
      b: "Lion",
      c: "Elephant",
      d: "Cheetah",
    },
    correctAnswer: "b",
  },
  {
    question: "What is the primary language spoken in Egypt?",
    answers: {
      a: "Arabic",
      b: "English",
      c: "French",
      d: "Spanish",
    },
    correctAnswer: "a",
  },
  {
    question: "What is the most widely spoken language in the world?",
    answers: {
      a: "Spanish",
      b: "English",
      c: "Mandarin Chinese",
      d: "Hindi",
    },
    correctAnswer: "c",
  },
  {
    question: "Which country is known as the Land of the Rising Sun?",
    answers: {
      a: "China",
      b: "Japan",
      c: "Thailand",
      d: "South Korea",
    },
    correctAnswer: "b",
  },
];

// Function to display messages
function displayMessage(type, message) {
  const icon = type === "success" ? "fas fa-thumbs-up" : "fas fa-exclamation";
  const color = type === "success" ? "green" : "red";
  
  $("#notification-overlay").css({ display: "flex", zIndex: 900000 });
  $("#icon-div").html(`<i class="${icon}"></i>`).css({ color });
  $("#message-div").text(message);
}

// Function to terminate the quiz
function terminateQuiz(message) {
  displayMessage("error", message);
  $("#quiz-overlay").css({ display: "none" });
  resetQuizUI();
}

// Function to reset quiz UI
function resetQuizUI() {
  $("#overlay-content h3 b").text("");
  $("#question-text .item-question").text("");
}

// Main Application Logic
let questions = [];
let currentQuestionIndex = 0;
let attempts = 0;
let timer;

$("#overlay-content h3 b").text(`1 of ${limit}`);

function updateProgressBar() {
  const progress = (attempts / limit) * 100;
  $("#progressBar").css({ width: `${progress}%` });
}

function displayQuestion(time) {
  const currentQuestion = questions[currentQuestionIndex];
  $("#quiz-overlay").css({ display: "flex" });
  $("#question-text .item-question").text(currentQuestion.question);
  
  // Clear previous options
  $("#answers").empty();
  for (const key in currentQuestion.answers) {
    const answerKey = key.toUpperCase();
    const option = `
      <div class="item-option">
        <div class="list">
            <input type="radio" name="answer" value="${key}" id="${key}">
            <label for="${key}">${currentQuestion.answers[key]}</label><br>
        </div>
      </div>
    `;
    $("#answers").append(option);
  }
  // Update status
  if(isActive !== true){
    isActive = true;
  }
  // Start timer
  startTimer(time);
}

function startTimer(time) {
  let timeLeft = time;
  timer = setInterval(() => {
    timeLeft--;
    $("#timer b").text(timeLeft < 10 ? "0" + timeLeft : timeLeft);
    
    if (timeLeft <= 0) {
      clearInterval(timer);
      terminateQuiz("Time's up! You did not answer in time.");
    }
  }, 1000);
}

function checkAnswer() {
  const selectedAnswer = document.querySelector('input[name="answer"]:checked');
  if (!selectedAnswer) return; // No answer selected

  clearInterval(timer); // Stop timer
  const answerValue = selectedAnswer.value;
  const currentQuestion = questions[currentQuestionIndex];

  if (answerValue === currentQuestion.correctAnswer) {
    currentQuestionIndex++;
    attempts++;
    updateProgressBar();
    
    if (currentQuestionIndex < questions.length) {
      displayQuestion(15);
      $("#overlay-content h3 b").text(
        `${currentQuestionIndex + 1} of ${limit}`
      );
    } else {
      cheerSound.play();
      displayMessage("success", `You've completed the ${limit}-question quiz!`);
      setTimeout(() => {
        cheerSound.pause();
      }, 4000);
    }
  } else {
    wrongSound.play();
    terminateQuiz("Incorrect answer! Please retake the quiz.");
    setTimeout(() => {
      wrongSound.pause();
    }, 4000);
  }
}

function shuffleArray(array, limit) {
  const shuffled = array.slice();
  for (let i = shuffled.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
  }
  return shuffled.slice(0, limit);
}

function fetchQuestions() {
  questions = shuffleArray(myQuestions, limit);
  $("#quiz-overlay").css({ display: "flex" });
  displayQuestion(15);
}

// Event listeners
$("#close-overlay, #close-notification").on("click", function () {
  resetQuizUI();
  $("#notification-overlay, #quiz-overlay").css({ display: "none" });
  window.location.reload();
});

document.addEventListener("DOMContentLoaded", () => {
  $("#start").on("click", function(){
       fetchQuestions();
  });
  
  $("#answers").on("change", function(){
      setTimeout(function(){
        checkAnswer();
      }, 800);
  });

  // Back link click
  document.addEventListener('popstate', function() {
      if(isActive === true){
        terminateQuiz("Page navigation changed");
      }
  });
  
  // Visibility change event
  document.addEventListener("visibilitychange", () => {
    if (document.hidden) {
        if(isActive === true){
            terminateQuiz("The quiz has been terminated due to visibility change.");
        }
    }
  });
  
   // Resize event
  document.addEventListener("resize", () => {
    if(isActive === true){
        terminateQuiz("The quiz has been terminated due to page resize.");
    }
  });

  // Prevent context menu
  document.getElementById("question-text").addEventListener("contextmenu", e => {
    e.preventDefault();
  });

  // Disable Ctrl+C shortcut
  document.addEventListener("keydown", e => {
    if (e.ctrlKey && (e.key === "c" || e.key === "C")) {
      e.preventDefault();
    }
  });
});
