const jokeBox = document.querySelector("#quote-p");
const newQuoteBtn = document.querySelector("#new-quote-btn");

const configHeader = { headers: { Accept: "application/json" } };

const randomJoke = async () => {
  try {
    jokeBox.textContent = "Loading...";

    const req = await axios.get("https://icanhazdadjoke.com/", configHeader);
    console.log(req.data);
    jokeBox.textContent = req.data.joke;
  } catch (err) {
    jokeBox.textContent = "Failed to fetch quote.";
    console.error("REJECTED", err);
  }
};

newQuoteBtn.addEventListener("click", randomJoke);
