<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
</head>

<body>
    <h2>Connexion</h2>

    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/login">
        <label>Email :</label>
        <input type="email" name="email" required>
        
        <label>Mot de passe :</label>
        <input type="password" name="password" required>
        
        <button type="submit">Se connecter</button>
    </form>
</body>

</html>
