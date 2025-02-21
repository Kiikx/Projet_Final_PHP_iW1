<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tripipics </title>
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
                        <td class="members-actions">
                            <?php if ($_SESSION['user_id'] !== $member['user_id']): ?>
                                <button class="button edit-role-btn" data-user-id="<?= $member['user_id'] ?>" data-role="<?= $member['role'] ?>">Modifier</button>
                                <form method="POST" action="/group/remove-member">
                                    <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
                                    <input type="hidden" name="user_id" value="<?= $member['user_id'] ?>">
                                    <button type="submit" class="button button--danger">Supprimer</button>
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
                    <?php if (Group::isOwner($_SESSION['user_id'], $group['id']) || $_SESSION['user_id'] == $photo['user_id']): ?>
                        <form method="POST" action="/photo/delete">
                            <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                            <button type="submit" class="delete-photo-btn">❌ Supprimer</button>
                        </form>
                    <?php endif; ?>
                    <?php if (Group::isOwner($_SESSION['user_id'], $group['id']) || $_SESSION['user_id'] == $photo['user_id']): ?>
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

    <div id="role-modal" class="group-modal">
        <div class="group-modal--content">
            <button class="group-modal--close">&times;</button>
            <h3>Modifier le rôle</h3>
            <form method="POST" action="/group/change-role">
                <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
                <input type="hidden" name="user_id" id="modal-user-id">
                <label for="role">Nouveau rôle :</label>
                <select name="role" id="modal-role">
                    <option value="read">Lecture</option>
                    <option value="write">Écriture</option>
                </select>
                <button type="submit" class="button">Mettre à jour</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modal = document.getElementById("role-modal");
            const modalUserId = document.getElementById("modal-user-id");
            const modalRole = document.getElementById("modal-role");
            const closeModal = document.querySelector(".group-modal--close");

            document.querySelectorAll(".edit-role-btn").forEach(button => {
                button.addEventListener("click", function() {
                    modal.classList.add("group-modal--open");
                    modalUserId.value = this.dataset.userId;
                    modalRole.value = this.dataset.role;
                });
            });

            closeModal.addEventListener("click", function() {
                modal.classList.remove("group-modal--open");
            });

            window.addEventListener("click", function(event) {
                if (event.target === modal) {
                    modal.classList.remove("group-modal--open");
                }
            });
        });
    </script>

</body>

</html>