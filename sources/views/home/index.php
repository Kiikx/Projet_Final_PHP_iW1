<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="../../dist/css/main.css">
</head>

<body>

    <header>
        <h1>Bienvenue sur l'application de partage de photos</h1>

        <?php if ($user): ?>
            <p>Bonjour, <strong><?= htmlspecialchars($user->username) ?></strong> !</p>
            <a href="/logout">Déconnexion</a>
        <?php else: ?>
            <p><a href="/login">Connexion</a> | <a href="/register">Inscription</a></p>
        <?php endif; ?>
    </header>

    <main>
        <p>Partagez vos photos de road trips avec vos amis !</p>
    </main>

</body>

</html>