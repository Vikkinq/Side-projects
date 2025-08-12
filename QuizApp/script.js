document.addEventListener("DOMContentLoaded", function () {
  // Highlight selected option
  const options = document.querySelectorAll(".option");

  options.forEach((option) => {
    option.addEventListener("click", function () {
      // First remove selected class from all options
      options.forEach((opt) => {
        opt.classList.remove("selected");
      });

      // Add selected class to clicked option
      this.classList.add("selected");

      // Check the radio button
      const radio = this.querySelector('input[type="radio"]');
      radio.checked = true;
    });
  });

  // Auto-submit when time runs out (if timer is implemented)
  // This is a placeholder for future timer functionality

  // Confirm before leaving quiz
  const form = document.getElementById("quiz-form");
  if (form) {
    window.addEventListener("beforeunload", function (e) {
      // Cancel the event
      e.preventDefault();
      // Chrome requires returnValue to be set
      e.returnValue = "";
    });

    // Don't show confirmation when form is submitted normally
    form.addEventListener("submit", function () {
      window.removeEventListener("beforeunload", function () {});
    });
  }

  // Animation for result page
  const scoreCircle = document.querySelector(".score-circle");
  if (scoreCircle) {
    scoreCircle.classList.add("animate");
  }
});
