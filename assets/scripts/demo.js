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
