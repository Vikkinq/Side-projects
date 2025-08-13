const input = document.querySelector("#guess-input");
const guessBtn = document.querySelector("#guess-btn");
const feedback = document.querySelector("#feedback");
const attemptsCount = document.querySelector("#attempts-left");
const reset = document.querySelector("#reset-btn");
const history = document.querySelector("#history");

// const guessGame = {};

let prevGuess = "";

let remainingAttempts = 10;
let isGameOver = false;
let max = 100;

let randomNum = Math.floor(Math.random() * max + 1);
console.log(randomNum);

function guessSystem() {
  if (!isGameOver) {
    if (Number(input.value) === randomNum) {
      isGameOver = true;
      feedback.textContent = "You Won!";
    } else if (remainingAttempts === 0) {
      isGameOver = true;
      feedback.textContent = "You Lost!";
    } else if (Number(input.value) > randomNum) {
      remainingAttempts--;
      prevGuess = input.value;
      attemptsCount.textContent = remainingAttempts;
      feedback.textContent = "Too High";
      input.value = "";
    } else {
      remainingAttempts--;
      prevGuess = input.value;
      attemptsCount.textContent = remainingAttempts;
      feedback.textContent = "Too Low";
      input.value = "";
    }
    // prevGuess.splice(1, prevGuess.length(), Number(input.value));
    history.textContent = prevGuess;
  }
}

function resetButton() {
  isGameOver = false;
  remainingAttempts = 10;
  attemptsCount.textContent = remainingAttempts;
  feedback.textContent = "";
  input.value = "";
  randomNum = Math.floor(Math.random() * 100 + 1);
}

guessBtn.addEventListener("click", guessSystem);
reset.addEventListener("click", resetButton);
