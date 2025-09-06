const animeForm = document.querySelector("#anime-form");
const searchBar = document.querySelector("#anime-input");
const filterOption = document.querySelector("#filter-anime");
const animeDisplay = document.querySelector("#gridSection");
const animeGrid = document.querySelector("#gridSection");

const jikan_API = "https://api.jikan.moe/v4/anime?limit=20";

let displayCount = 10;
let allAnime = [];

animeForm.addEventListener("submit", function (e) {
  e.preventDefault();

  showAnimeList();
});

filterOption.addEventListener("change", showAnimeList);

async function generateAnime() {
  try {
    const animeRequest = await axios.get(jikan_API);
    allAnime = animeRequest.data.data.map((q) => ({
      title: q.title,
      score: q.score,
      image: q.images.jpg.image_url,
      synopsis: q.synopsis,
      type: q.type,
    }));
    console.log(allAnime);
    showAnimeList();
  } catch (error) {
    console.log("REJECTED", error);
  }
}

function showAnimeList() {
  animeGrid.innerHTML = "";

  let filteredAnime = filterAnime();

  filteredAnime.slice(0, displayCount).forEach((all) => {
    const animeCard = document.createElement("div");
    const animeImage = document.createElement("img");
    const animeTitle = document.createElement("h4");
    const animeScore = document.createElement("span");
    const animeSynopsis = document.createElement("p");

    animeCard.className = "anime-card";

    animeImage.src = all.image;
    animeTitle.textContent = all.title;
    animeScore.textContent = all.score;
    animeSynopsis.textContent = all.synopsis;

    animeCard.append(animeImage, animeTitle, animeScore, animeSynopsis);
    animeGrid.append(animeCard);
  });
}

function filterAnime() {
  const searchTerm = searchBar.value.toLowerCase();
  const filterTerm = filterOption.value.toLowerCase();

  return allAnime.filter((f) => {
    const matchSearch = f.title.toLowerCase().includes(searchTerm);
    const matchesType =
      filterTerm === " " || f.type.toLowerCase().includes(filterTerm);

    return matchSearch && matchesType;
  });
}

generateAnime();
