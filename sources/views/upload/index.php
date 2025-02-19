<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload d'image</title>
    <link rel="stylesheet" href="/dist/css/main.css">
</head>
<body>
    <h2 class="title">Publiez votre PIPIcs</h2>
    
    <form id="upload-form" action="/upload" method="POST" enctype="multipart/form-data" class="drag-uploader">
        <label for="group_id">ID du groupe :</label>
        <input type="number" name="group_id" required>
        
        <br><br>

        <div class="drag-uploader__drop-area" id="drop-area">
            <p>Glissez & déposez votre image ici ou <label for="file-input" class="drag-uploader__label"><strong>cliquez pour sélectionner<strong></label></p>
            <input type="file" id="file-input" name="image" accept="image/jpeg, image/png, image/gif, image/webp" class="drag-uploader__file-input" required>
            <div class="drag-uploader__preview-container" id="preview-container"></div>
        </div>

        <button class="button" type="submit">Envoyer</button>
    </form>

    <script src="/src/js/dragUploader.js"></script>
</body>
</html>
