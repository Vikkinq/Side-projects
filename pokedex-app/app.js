const searchBar = document.querySelector("#search");
const filterElements = document.querySelector("#filter-type");
const filterRegion = document.querySelector("#filter-region");
const pokemonGridSection = document.querySelector("#pokemon-grid");
const loadMore = document.querySelector("#load-more");

const firstGen_API = "https://pokeapi.co/api/v2/pokemon?limit=151";

let allPokemon = [];
let displayCount = 12;

searchBar.addEventListener("input", renderPokemon);
filterElements.addEventListener("change", renderPokemon);
filterRegion.addEventListener("change", renderPokemon);

loadMore.addEventListener("click", function () {
  displayCount += 20;
  renderPokemon();
});

async function generatePokemon() {
  try {
    console.log("APPROVED");
    const req = await axios.get(firstGen_API);
    const promises = req.data.results.map((p) => axios.get(p.url));

    const results = await Promise.all(promises);

    allPokemon = results.map((res) => ({
      name: res.data.name,
      img: res.data.sprites.front_default,
      types: res.data.types.map((t) => t.type.name),
      id: res.data.id,
    }));

    renderPokemon();
  } catch (err) {
    console.log("REJECTED", err);
  }
}

function renderPokemon() {
  pokemonGridSection.innerHTML = ""; // Clear grid first

  // Apply filters before rendering
  let filtered = filterPokemon();

  filtered.slice(0, displayCount).forEach((p) => {
    const pokemonCards = document.createElement("div");
    const pokemonImg = document.createElement("img");
    const pokemonName = document.createElement("p");

    pokemonCards.className = "pokemon-card";
    pokemonImg.src = p.img;
    pokemonName.textContent = p.name;

    pokemonCards.append(pokemonImg, pokemonName);
    pokemonGridSection.append(pokemonCards);
  });
}

function filterPokemon() {
  const searchTerm = searchBar.value.toLowerCase();
  const typeFilter = filterElements.value.toLowerCase();
  const regionFilter = filterRegion.value.toLowerCase();

  return allPokemon.filter((p) => {
    const matchesSearch = p.name.toLowerCase().includes(searchTerm);
    const matchesType = typeFilter === "" || p.types.includes(typeFilter);

    let matchesRegion = true;
    if (regionFilter === "kanto") {
      matchesRegion = p.id >= 1 && p.id <= 151;
    }

    return matchesSearch && matchesType && matchesRegion;
  });
}

generatePokemon();
