<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload d'image</title>
</head>
<body>
    <h2>Upload une image</h2>
    
    <form action="/upload" method="POST" enctype="multipart/form-data">
        <label for="group_id">ID du groupe :</label>
        <input type="number" name="group_id" required>
        
        <br><br>

        <label for="image">Sélectionne une image :</label>
        <input type="file" name="image" accept="image/jpeg, image/png, image/gif, image/webp" required>

        <br><br>

        <button type="submit">Uploader</button>
    </form>
</body>
</html>
