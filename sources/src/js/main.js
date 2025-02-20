document.addEventListener("DOMContentLoaded", () => {
    console.log("JS chargé");

    const themeToggle = document.getElementById("theme-toggle");

    if (!themeToggle) {
        console.error("Bouton non trouvé !");
        return;
    }

    console.log("Bouton trouvé !");

    // Vérifie si un thème est stocké dans localStorage, sinon applique "light"
    const currentTheme = localStorage.getItem("theme") || "light";
    document.documentElement.setAttribute("data-theme", currentTheme);
    themeToggle.textContent = currentTheme === "dark" ? "☀️" : "🌙";

    themeToggle.addEventListener("click", () => {
        console.log("Bouton cliqué !");
        const newTheme = document.documentElement.getAttribute("data-theme") === "dark" ? "light" : "dark";
        document.documentElement.setAttribute("data-theme", newTheme);
        localStorage.setItem("theme", newTheme);
        themeToggle.textContent = newTheme === "dark" ? "☀️" : "🌙";
        console.log("Nouveau thème :", newTheme);
    });
});
