<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="../../dist/css/main.css">
</head>

<body>

    <form class="form" action="/login" method="POST">
        <h1>Connexion</h1>

        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <p class="form__error-message"><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        <?php endif; ?>

        <label for="email">Adresse e-mail</label>
        <div class="form__input-wrapper">
            <span class="icon">&#9993;</span>
            <input type="email" id="email" name="email" placeholder="Entrez votre adresse e-mail" required>
        </div>

        <label for="password">Mot de passe</label>
        <div class="form__input-wrapper">
            <span class="icon">&#128274;</span>
            <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>
        </div>

        <a href="/password/forgot">Mot de passe oublié ?</a>

        <button type="submit" >Se connecter</button>
    </form>
</body>

</html>