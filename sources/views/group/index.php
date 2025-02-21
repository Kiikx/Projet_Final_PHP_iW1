<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Groupes</title>
    <link rel="stylesheet" href="../../dist/css/main.css">
</head>

<body>

    <?php
    include_once "includes/navbar.php";
    renderNavbar();

    ?>
    <h2 class="text-center">Mes Groupes</h2>

    <section class="group-list">
        <?php if (empty($groups)): ?>
            <p class="text-center">Vous n'avez pas encore de groupes.</p>
        <?php else: ?>
            <?php foreach ($groups as $group): ?>
                <div class="group-list__item">
                    <div class="group-list__item--content">
                        <div class="group-list__item--name"><?php echo ($group["name"]) ?></div>
                        <div class="group-list__item--members"><?php echo (GroupMember::getMemberCount($group["id"])) ?></div>
                    </div>
                    <div class="group-list__item--links">
                        <a href=<?php echo ("/group/" . $group["id"]) ?> class="group-list__item--link">Voir</a>
                        <?php if ($group["owner_id"] == $_SESSION["user_id"]): ?>
                            <button class="group-list__item--button" onclick="openEditPopup(<?php echo $group['id']; ?>, '<?php echo $group['name']; ?>')">Modifier</button>
                            <form method="POST" action=<?php echo ("/group/delete/" . $group["id"]) ?>>
                                <input type="hidden" name="group_id" value=<?php echo ($group["id"]); ?>>
                                <button type="submit" class="group-list__item--button">Supprimer</button>
                            </form>
                        <?php endif; ?>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        </div>
    </section>

    <h2 class="text-center">Créer un Groupe</h2>

    <form class="form" method="POST" action="/group/create">
        <label>Nom du groupe :</label>
        <div class="form__input-wrapper">
            <input type="text" name="name" placeholder="Nom du groupe" required>
        </div>
        <button type="submit">Créer</button>
    </form>

    <div id="groupModal" class="group-modal">
        <div class="group-modal--content">
            <button class="group-modal--close" onclick="closeEditPopup()">×</button>
            <h2>Modifier le groupe</h2>
            <form id="editGroupForm" method="POST" action=<?php echo ("/group/update/" . $group["id"]) ?>>
                <input type="hidden" name="group_id" id="groupId">
                <label for="groupName">Nom du groupe :</label>
                <input type="text" name="group_name" id="groupName" required>
                <button type="submit" class="group-list__item--button">Enregistrer</button>
            </form>
        </div>
    </div>
    <script src="../../src/js/popup.js"></script>
    <script src="../../src/js/darkMode.js"></script>

</body>

</html>