<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploader une image</title>
</head>
<body>
    <h2>Publiez votre PIPIcs</h2>
    <form action="/upload" method="post" enctype="multipart/form-data">
        <input type="file" name="image" required>
        <p>jpeg, png, gif, webp jusqu'à 2MB</p>
        <button type="submit">Envoyer</button>
    </form>
</body>
</html>
