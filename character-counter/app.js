const input = document.querySelector("#input");
const counter = document.querySelector("#character-count");

let characterCounter = false;

function textCounter() {
  let inputCount = input.value;
  let lengthCount = inputCount.length;

  if (lengthCount >= 100) {
    counter.classList.add("error-text");
    input.classList.add("error-border");
    characterCounter = true;
  } else {
    counter.classList.remove("error-text");
    input.classList.remove("error-border");
    characterCounter = false;
  }

  if (lengthCount <= 100) {
    counter.textContent = lengthCount;
    // console.log(lengthCount);
  }
}

input.addEventListener("input", textCounter);
