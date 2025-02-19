<?php
function renderNavbar($pageTitle = "Tripipics") {
    if (isset($_SESSION["user_id"])) {
        require_once __DIR__ . "/../models/User.php";
        $user = User::findById($_SESSION["user_id"]);

    }else{
        $user = null;
    }
?>
<header class="navbar">
    <a href="/" class="navbar__logo"><?php echo htmlspecialchars($pageTitle); ?></a>
    <button class="navbar__toggle" id="navbar-toggle" aria-label="Ouvrir le menu">☰</button>
    <nav class="navbar__links" id="navbar-links">

        <?php if ($user): ?>
            <p class="navbar__user"><?php echo($user->username); ?></p>

            <a href="/groups" class="navbar__logout">Mes Groupes</a>

            <a href="/logout" class="navbar__logout">Déconnexion</a>
        <?php else: ?>
            <a href="/login" class="navbar__login">Connexion</a>
        <?php endif; ?>
    </nav>
</header>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const menuToggle = document.getElementById("navbar-toggle");
        const navLinks = document.getElementById("navbar-links");

        menuToggle.addEventListener("click", function() {
            navLinks.classList.toggle("navbar__links--active");
            menuToggle.setAttribute("aria-expanded", navLinks.classList.contains("navbar__links--active"));
        });
    });
</script>
<?php
}
?>
