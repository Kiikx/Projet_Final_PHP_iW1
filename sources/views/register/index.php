<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../dist/css/main.css">
    <title>Inscription</title>
</head>

<body>

    <form class="form" method="POST" action="/register">

        <h2>Inscription</h2>

        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <p class="form__error-message"><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        <?php endif; ?>

        <label>Email :</label>
        <div class="form__input-wrapper">
            <input type="email" name="email" placeholder="Entrez votre adresse e-mail" required>
        </div>

        <label>Nom d'utilisateur :</label>
        <div class="form__input-wrapper">
            <input type="text" name="username" placeholder="Entrez votre nom d'uttilisateur" required>
        </div>

        <label>Mot de passe :</label>
        <div class="form__input-wrapper">
            <input type="password" name="password" placeholder="Entrez votre mot de passe"required>
        </div>

        <label>Confirmer le mot de passe :</label>
        <div class="form__input-wrapper">
            <input type="password" name="password_confirm" placeholder="Confirmer votre mot de passe"required>
        </div>

        <button type="submit">S'inscrire</button>
    </form>
</body>

</html>