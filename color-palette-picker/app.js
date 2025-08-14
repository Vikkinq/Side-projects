const colorWheel = document.querySelector("#color-wheel");
const colorName = document.querySelector("#color-id");

function changeColor() {
  let color = colorWheel.value;

  document.body.style.backgroundColor = color;
  colorName.textContent = colorWheel.value.toUpperCase();

  console.log(colorWheel.value);
}

colorWheel.addEventListener("input", changeColor);
