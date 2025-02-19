<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="../../dist/css/main.css">
</head>


<body>
    
<?php
include_once "includes/navbar.php";
renderNavbar();
?>
<h1><?php echo htmlspecialchars($group['name']); ?></h1>

<?php if ($isMember): ?>
    <h2>Photos du groupe</h2>
    <?php if (!empty($photos)): ?>
        <div class="gallery">
            <?php foreach ($photos as $photo): ?>
                <img src="/uploads/group_<?php echo $group['id']; ?>/<?php echo $photo['filename']; ?>" alt="Photo">
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Aucune photo pour le moment.</p>
    <?php endif; ?>
<?php else: ?>
    <p>❌ Vous n'avez pas accès à ce groupe.</p>
<?php endif; ?>

</body>

</html>