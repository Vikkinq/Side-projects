const playerScore = document.querySelector("#playerScore");
const pChoiceH2 = document.querySelector("#playerChoice");
const computerImage = document.querySelector("#compImage");
const computerScore = document.querySelector("#computerScore");
const h2Result = document.querySelector("#result");

// Modal Query
const modeModal = document.querySelector("#modeModal");
const bestOf = document.querySelector("#bestOf");

const reset = document.querySelector("#reset");

const choices = ["rock", "paper", "scissor"];

let maxScore = 5;
let pScore = 0;
let cScore = 0;
let result = "";
let isGameOver = false;

document.querySelectorAll(".btn").forEach((btn) => {
  btn.addEventListener("click", function () {
    if (isGameOver) return;

    const playerChoice = btn.dataset.value;
    const computerChoice = choices[Math.floor(Math.random() * 3)];

    // 2. Update UI choices
    pChoiceH2.src = `img/${playerChoice}.png`;
    computerImage.src = `img/${computerChoice}.png`;

    // 3. Determine result
    if (playerChoice === computerChoice) {
      result = "It is a Tie!";
      h2Result.textContent = result;
      return; // Short-circuit to avoid overwriting tie
    }

    switch (playerChoice) {
      case "rock":
        result = computerChoice === "scissor" ? "You Win!" : "You Lose!";
        break;
      case "scissor":
        result = computerChoice === "paper" ? "You Win!" : "You Lose!";
        break;
      case "paper":
        result = computerChoice === "rock" ? "You Win!" : "You Lose!";
        break;
    }

    // 4. Update scores
    if (result === "You Win!") {
      victory();
    } else if (result === "You Lose!") {
      defeat();
    }

    // 5. Check game-over
    if (pScore >= maxScore || cScore >= maxScore) {
      isGameOver = true;
      h2Result.textContent = `${result} - Game Over!`;
      disableButtons(true);
    } else {
      h2Result.textContent = result;
    }
  });
});

modeModal.addEventListener("click", (e) => {
  if (e.target === modeModal) closeModal();
});

// Modal for PVP and PVE
modeModal.querySelectorAll("[data-mode]").forEach((btn) => {
  btn.addEventListener("click", () => {
    mode = btn.dataset.mode; // "PVP" or "PVC"
    maxScore = Math.ceil(Number(bestOf.value) / 2);
    closeModal();
    resetGame();
  });
});

document.querySelector("#openMode")?.addEventListener("click", openModal);
document.querySelector("#closeMode")?.addEventListener("click", closeModal);
reset.addEventListener("click", resetGame);

function victory() {
  pScore++;
  playerScore.textContent = pScore;
}

function defeat() {
  cScore++;
  computerScore.textContent = cScore;
}

function disableButtons(state) {
  document.querySelectorAll(".btn").forEach((btn) => {
    btn.disabled = state;
  });
}

function resetGame() {
  cScore = 0;
  pScore = 0;
  isGameOver = false;
  h2Result.textContent = "Make your move!";
  pChoiceH2.src = "img/question-mark.png";
  playerScore.textContent = "0";
  computerScore.textContent = "0";
  computerImage.src = "img/question-mark.png"; // Default image
  disableButtons(false);
}

function openModal() {
  modeModal.classList.remove("hidden");
  modeModal.setAttribute("aria-hidden", "false");
}

function closeModal() {
  modeModal.classList.add("hidden");
  modeModal.setAttribute("aria-hidden", "true");
}
