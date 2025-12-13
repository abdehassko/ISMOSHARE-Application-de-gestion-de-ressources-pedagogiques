// Notification scrpt

const notificationDropdown = document.getElementById("notificationDropdown");
const notificationIcon = document.querySelector(".notification-icon");

notificationIcon.addEventListener("click", () => {
  const isVisible = notificationDropdown.style.display === "block";
  notificationDropdown.style.display = isVisible ? "none" : "block";
});

window.addEventListener("click", (e) => {
  if (
    !notificationIcon.contains(e.target) &&
    !notificationDropdown.contains(e.target)
  ) {
    notificationDropdown.style.display = "none";
  }
});

//Scrpt pour ajouter un filtre

const filterModal = document.getElementById("filterModal");
const filterButton = document.querySelector(".filter-button");
const closeFilterModal = document.getElementById("closeFilterModal");

filterButton.addEventListener("click", () => {
  filterModal.style.display = "flex";
});

closeFilterModal.addEventListener("click", () => {
  filterModal.style.display = "none";
});

window.addEventListener("click", (e) => {
  if (e.target === filterModal) {
    filterModal.style.display = "none";
  }
});

document.getElementById("applyFilter").addEventListener("click", () => {
  const filiere = document.getElementById("filterFiliere").value;
  const module = document.getElementById("filterModule").value;
  const date = document.getElementById("filterDate").value;

  console.log("Filter applied:", { filiere, module, date });
  filterModal.style.display = "none";
});
