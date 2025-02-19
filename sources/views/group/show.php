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
        <h2>Membres du groupe</h2>
        <table class="members-table">
            <tr>
                <th>Nom</th>
                <th>Rôle</th>
                <?php if (Group::isOwner($_SESSION['user_id'], $group['id'])): ?>
                    <th>Actions</th>
                <?php endif; ?>
            </tr>
            <?php foreach (GroupMember::getGroupMembers($group['id']) as $member): ?>
                <tr>
                    <td><?= htmlspecialchars($member['username']) ?></td>
                    <td><?= htmlspecialchars($member['role']) ?></td>
                    <?php if (Group::isOwner($_SESSION['user_id'], $group['id'])): ?>
                        <td>
                            <?php if ($_SESSION['user_id'] !== $member['user_id']): ?>
                                <form method="POST" action="/group/change-role">
                                    <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
                                    <input type="hidden" name="user_id" value="<?= $member['user_id'] ?>">
                                    <select name="role">
                                        <option value="read" <?= $member['role'] === 'read' ? 'selected' : '' ?>>Lecture</option>
                                        <option value="write" <?= $member['role'] === 'write' ? 'selected' : '' ?>>Écriture</option>
                                    </select>
                                    <button type="submit">Modifier</button>
                                </form>

                                <form method="POST" action="/group/remove-member">
                                    <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
                                    <input type="hidden" name="user_id" value="<?= $member['user_id'] ?>">
                                    <button type="submit">Supprimer</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </table>

        <?php if (Group::isOwner($_SESSION['user_id'], $group['id'])): ?>
            <h2>Inviter un membre</h2>
            <form method="POST" id="invite-member-form">
                <input type="hidden" name="group_id" value="<?= $group['id'] ?>">

                <label for="email">Email :</label>
                <input type="email" name="email" id="email" required placeholder="Email de l'utilisateur">

                <label for="role">Rôle :</label>
                <select name="role">
                    <option value="read">Lecture</option>
                    <option value="write">Écriture</option>
                </select>

                <button type="submit">Inviter</button>
            </form>
        <?php endif; ?>

        <script>
            document.getElementById("invite-member-form").addEventListener("submit", function(e) {
                e.preventDefault();
                const groupId = document.querySelector("input[name='group_id']").value;
                const email = document.getElementById("email").value;
                const role = document.querySelector("select[name='role']").value;

                fetch(`/group/${groupId}/add-member`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: `email=${encodeURIComponent(email)}&role=${role}`
                    })
                    .then(response => response.text())
                    .then(data => {
                        alert(data);
                        location.reload();
                    })
                    .catch(error => console.error("Erreur:", error));
            });
        </script>

        <?php if (Group::isOwner($_SESSION['user_id'], $group['id']) || GroupMember::getRole($_SESSION['user_id'], $group['id']) === 'write'): ?>
            <h2>Uploader une photo</h2>
            <form action="/upload" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="group_id" type="number" value="<?= $group['id'] ?>" required>
                <input type="file" name="image" accept="image/jpeg, image/png, image/gif, image/webp" required>
                <button type="submit">Uploader</button>
            </form>
        <?php endif; ?>

        <h2>Photos du groupe</h2>
        <?php if (!empty($photos)): ?>
            <div class="gallery">
                <?php foreach ($photos as $photo): ?>
                    <img src="/uploads/group_<?php echo $group['id']; ?>/<?php echo $photo['filename']; ?>" alt="Photo">
                    <?php if (Group::isOwner($_SESSION['user_id'], $group['id']) || $_SESSION['user_id'] === $photo['user_id']): ?>
                        <form method="POST" action="/photo/delete">
                            <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                            <button type="submit" class="delete-photo-btn">❌ Supprimer</button>
                        </form>
                    <?php endif; ?>
                    <?php if (Group::isOwner($_SESSION['user_id'], $group['id']) || $_SESSION['user_id'] === $photo['user_id']): ?>
                        <?php if (empty($photo['public_token'])): ?>
                            <form method="POST" action="/photo/share">
                                <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                                <button type="submit" class="share-photo-btn">🔗 Partager</button>
                            </form>
                        <?php else: ?>
                            <p>🔗 Lien de partage : <a href="/photo/<?= $photo['public_token'] ?>" target="_blank">Voir la photo</a></p>
                            <form method="POST" action="/photo/unshare">
                                <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                                <button type="submit" class="unshare-photo-btn">❌ Supprimer le lien</button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Aucune photo pour le moment.</p>
        <?php endif; ?>
    <?php else: ?>
        <p>❌ Vous n'avez pas accès à ce groupe.</p>
    <?php endif; ?>

    <?php if (GroupMember::isMember($_SESSION['user_id'], $group['id']) && (!Group::isOwner($_SESSION['user_id'], $group['id']) || GroupMember::hasMultipleOwners($group['id']))): ?>
        <form method="POST" action="/group/<?= $group['id'] ?>/leave">
            <button type="submit" class="leave-btn">Quitter le groupe</button>
        </form>
    <?php endif; ?>


</body>

</html>