<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
</head>
<body>
    <h2>Réinitialisation du mot de passe</h2>

    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/password/reset?token=<?= htmlspecialchars($_GET["token"] ?? "") ?>">
        <label>Nouveau mot de passe :</label>
        <input type="password" name="password" required>
        
        <label>Confirmer le mot de passe :</label>
        <input type="password" name="password_confirm" required>

        <button type="submit">Réinitialiser</button>
    </form>
</body>
</html>
