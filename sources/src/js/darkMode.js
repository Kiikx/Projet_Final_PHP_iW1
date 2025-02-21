// console.log("JS chargé");
// document.addEventListener("DOMContentLoaded", () => {
//     const themeToggle = document.getElementById("theme-toggle");
//     const currentTheme = localStorage.getItem("theme") || "light";
//     if (!themeToggle) {
//         console.error("Bouton non trouvé !");
//         return;
//     }

//     console.log("Bouton trouvé !");
//     document.documentElement.setAttribute("data-theme", currentTheme);
//     themeToggle.textContent = currentTheme === "dark" ? "☀️" : "🌙";

//     themeToggle.addEventListener("click", () => {
//         const newTheme = document.documentElement.getAttribute("data-theme") === "dark" ? "light" : "dark";
//         document.documentElement.setAttribute("data-theme", newTheme);
//         localStorage.setItem("theme", newTheme);
//         themeToggle.textContent = newTheme === "dark" ? "☀️" : "🌙";
//     });
// });


document.addEventListener("DOMContentLoaded", function () {
    const body = document.body;
    const darkModeToggle = document.querySelector(".navbar__darkmode");

    // Vérifier si le mode sombre est activé dans localStorage
    if (localStorage.getItem("darkMode") === "enabled") {
        body.classList.add("dark");
    }

    // Fonction pour activer/désactiver le dark mode
    function toggleDarkMode() {
        body.classList.toggle("dark");

        if (body.classList.contains("dark")) {
            localStorage.setItem("darkMode", "enabled");
        } else {
            localStorage.setItem("darkMode", "disabled");
        }
    }

    // Ajouter l'événement au bouton si trouvé
    if (darkModeToggle) {
        darkModeToggle.addEventListener("click", function (event) {
            event.preventDefault(); // Empêcher le changement de page si le bouton est un `<a>`
            toggleDarkMode();
        });
    }
});
