const generateBtn = document.querySelector("#btn");
const dogImg = document.querySelector("#dog");
const catImg = document.querySelector("#cat");
const dogLoading = document.querySelector("#dog-loading");
const catLoading = document.querySelector("#cat-loading");

const dogAPI = "https://dog.ceo/api/breeds/image/random";
const catAPI = "https://api.thecatapi.com/v1/images/search";

const generateImage = async () => {
  try {
    dogLoading.classList.remove("hidden");
    catLoading.classList.remove("hidden");
    dogImg.style.display = "none";
    catImg.style.display = "none";

    const dogRequest = await axios.get(dogAPI);
    const catRequest = await axios.get(catAPI);

    dogImg.src = dogRequest.data.message;
    catImg.src = catRequest.data[0].url;

    dogImg.onload = () => {
      dogLoading.classList.add("hidden");
      dogImg.style.display = "block";
    };

    catImg.onload = () => {
      catLoading.classList.add("hidden");
      catImg.style.display = "block";
    };
  } catch (err) {
    console.log("Error fetching images:", err);
  }
};

generateBtn.addEventListener("click", generateImage);
