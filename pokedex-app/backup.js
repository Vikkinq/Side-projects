const searchBar = document.querySelector("#search");
const filterElements = document.querySelector("#filter-type");
const filterRegion = document.querySelector("#filter-region");
const pokemonGridSection = document.querySelector("#pokemon-grid");
const loadMore = document.querySelector("#load-more");

const firstGen_API = "https://pokeapi.co/api/v2/pokemon?limit=151";

let allPokemon = []; // Store fetched Pokémon data
let displayedCount = 12; // Start with 12

// Fetch All Pokémon (First Gen)
async function fetchPokemon() {
  try {
    const requestPokemon = await axios.get(firstGen_API);

    // Fetch details for all Pokémon
    const promises = requestPokemon.data.results.map((p) => axios.get(p.url));
    const results = await Promise.all(promises);

    // Store Pokémon objects { name, img, types }
    allPokemon = results.map((res) => ({
      name: res.data.name,
      img: res.data.sprites.front_default,
      types: res.data.types.map((t) => t.type.name),
      id: res.data.id,
    }));

    renderPokemon(); // Render first batch
  } catch (err) {
    console.log("REJECTED", err);
  }
}

// Render Pokémon into the DOM
function renderPokemon() {
  pokemonGridSection.innerHTML = ""; // Clear grid first

  // Apply filters before rendering
  let filtered = filterPokemon();

  // Only show "displayedCount" Pokémon
  filtered.slice(0, displayedCount).forEach((p) => {
    const pokemonCards = document.createElement("div");
    pokemonCards.className = "pokemon-card";

    const pokemonImg = document.createElement("img");
    pokemonImg.src = p.img;

    const pokemonName = document.createElement("p");
    pokemonName.textContent = p.name;

    pokemonCards.append(pokemonImg, pokemonName);
    pokemonGridSection.append(pokemonCards);
  });
}

// Combined Filtering Function
function filterPokemon() {
  const searchTerm = searchBar.value.toLowerCase();
  const typeFilter = filterElements.value.toLowerCase();
  const regionFilter = filterRegion.value.toLowerCase();

  return allPokemon.filter((p) => {
    const matchesSearch = p.name.toLowerCase().includes(searchTerm);

    const matchesType = typeFilter === "" || p.types.includes(typeFilter);

    // Example: region logic (Gen 1 = ID 1-151, Johto = 152-251 etc.)
    let matchesRegion = true;
    if (regionFilter === "kanto") {
      matchesRegion = p.id >= 1 && p.id <= 151;
    } else if (regionFilter === "johto") {
      matchesRegion = p.id >= 152 && p.id <= 251;
    }

    return matchesSearch && matchesType && matchesRegion;
  });
}

// Event Listeners
searchBar.addEventListener("input", renderPokemon);
filterElements.addEventListener("change", renderPokemon);
filterRegion.addEventListener("change", renderPokemon);

loadMore.addEventListener("click", () => {
  displayedCount += 20; // Add 20 more Pokémon
  renderPokemon();
});

// Init
fetchPokemon();
