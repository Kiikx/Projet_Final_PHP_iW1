<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../dist/css/main.css">
    <title>Mot de passe oublié</title>
</head>

<body>


    <form class="form" method="POST" action="/password/forgot">
        <h2>Réinitialiser le mot de passe</h2>

        <?php if (!empty($errors)): ?>
                    <?php foreach ($errors as $error): ?>
                        <p class="form__error-message"><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
                <p class="form__sucess-message"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
        <label>Email :</label>
        <div class="form__input-wrapper">
            <input type="email" name="email" placeholder="Entrez votre email">
        </div>
        <button type="submit">Envoyer le lien</button>

    </form>
</body>

</html>