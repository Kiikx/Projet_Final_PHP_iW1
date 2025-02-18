<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../dist/css/main.css">
    <title>Réinitialisation du mot de passe</title>
</head>

<body>


    <form class="form" method="POST" action="/password/reset?token=<?= htmlspecialchars($_GET["token"] ?? "") ?>">

        <h2>Réinitialisation du mot de passe</h2>

        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <p class="form__error-message"><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        <?php endif; ?>

        <label>Nouveau mot de passe :</label>
        <div class="form__input-wrapper">
            <input type="password" name="password" placeholder="Nouveau mot de passe">
        </div>
        <label>Confirmer le mot de passe :</label>
        <div class="form__input-wrapper">
            <input type="password" name="password_confirm" placeholder="Confirmer le nouveau mot de passe">
        </div>
        <button type="submit">Réinitialiser</button>
    </form>
</body>

</html>