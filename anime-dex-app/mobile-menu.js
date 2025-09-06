document.addEventListener("DOMContentLoaded", () => {
  const burgerToggle = document.getElementById("burger-toggle");
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("sidebar-overlay");

  function toggleSidebar() {
    burgerToggle.classList.toggle("active");
    sidebar.classList.toggle("active");
    overlay.classList.toggle("active");
  }

  burgerToggle.addEventListener("click", toggleSidebar);
  overlay.addEventListener("click", toggleSidebar);

  // Close sidebar when clicking on main content on mobile
  document.addEventListener("click", (e) => {
    if (
      window.innerWidth <= 768 &&
      !sidebar.contains(e.target) &&
      !burgerToggle.contains(e.target) &&
      sidebar.classList.contains("active")
    ) {
      toggleSidebar();
    }
  });
});
