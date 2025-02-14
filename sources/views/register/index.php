<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
</head>

<body>
    <h2>Inscription</h2>

    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/register">
        <label>Email :</label>
        <input type="email" name="email" required>
        
        <label>Nom d'utilisateur :</label>
        <input type="text" name="username" required>
        
        <label>Mot de passe :</label>
        <input type="password" name="password" required>
        
        <label>Confirmer le mot de passe :</label>
        <input type="password" name="password_confirm" required>
        
        <button type="submit">S'inscrire</button>
    </form>
</body>

</html>
