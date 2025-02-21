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

        <div class="group-members">
            <h2>Membres du groupe</h2>
            <ul class="group-members__list">
                <?php foreach (GroupMember::getGroupMembers($group['id']) as $member): ?>
                    <li class="group-members__item">
                        <div class="group-members__info">
                            <span class="group-members__username"><?= htmlspecialchars($member['username']) ?></span>
                            <span class="group-members__role role--<?= strtolower($member['role']) ?>">
                                <?= htmlspecialchars($member['role']) ?>
                            </span>
                        </div>
                        <?php if (Group::isOwner($_SESSION['user_id'], $group['id'])): ?>
                            <div class="group-members__actions">
                                <?php if ($_SESSION['user_id'] !== $member['user_id']): ?>
                                    <button class="button edit-role-btn" data-user-id="<?= $member['user_id'] ?>" data-role="<?= $member['role'] ?>">
                                        Modifier
                                    </button>
                                    <form method="POST" action="/group/remove-member">
                                        <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
                                        <input type="hidden" name="user_id" value="<?= $member['user_id'] ?>">
                                        <button type="submit" class="button button--danger">Supprimer</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php if (Group::isOwner($_SESSION['user_id'], $group['id'])): ?>
            <div class="group-member-add">
                <h2 class="group-member-add__title">Ajouter un membre</h2>
                <form method="POST" class="form" id="invite-member-form">
                    <input type="hidden" name="group_id" value="<?= $group['id'] ?>">

                    <label for="email" class="form__label">Email :</label>
                    <div class="form__input-wrapper">
                        <input type="email" name="email" id="email" required placeholder="Email de l'utilisateur">
                    </div>

                    <label for="role" class="form__label">Rôle :</label>
                    <select name="role" id="role">
                        <option value="read">Lecture</option>
                        <option value="write">Écriture</option>
                    </select>

                    <button type="submit" class="form__button">Inviter</button>
                </form>
            </div>
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

        <?php if (Group::isOwner($_SESSION['user_id'], $group['id']) || GroupMember::getRole($_SESSION['user_id'], $group['id']) == 'write'): ?>
            <section class="container">
                <h2 class="title">Uploader une PIPIcs</h2>
                <form id="upload-form" action="/upload" method="POST" enctype="multipart/form-data" class="drag-uploader">
                    <input type="hidden" name="group_id" value="<?= $group['id'] ?>">

                    <div class="drag-uploader__drop-area" id="drop-area">
                        <p id="drop-area-text">Glissez & déposez votre image ici ou
                            <label for="file-input" class="drag-uploader__label"><strong>cliquez pour sélectionner</strong></label>
                        </p>
                        <input type="file" id="file-input" name="image" accept="image/jpeg, image/png, image/gif, image/webp" class="drag-uploader__file-input" required />
                        <div class="drag-uploader__preview-container" id="preview-container"></div>
                    </div>

                    <button id="upload-button" class="button" type="submit">Envoyer</button>
                    <button id="delete-button" class="button button--danger" type="button" style="display: none;">Supprimer</button>
                </form>
            </section>

            <script>
                document.getElementById('file-input').addEventListener('change', function(event) {
                    const fileInput = event.target;
                    const dropAreaText = document.getElementById('drop-area-text');
                    const deleteButton = document.getElementById('delete-button');

                    if (fileInput.files.length > 0) {
                        dropAreaText.textContent = `Fichier sélectionné: ${fileInput.files[0].name}`;
                        deleteButton.style.display = 'inline-block';
                    } else {
                        dropAreaText.innerHTML = `Glissez & déposez votre image ici ou
                            <label for="file-input" class="drag-uploader__label"><strong>cliquez pour sélectionner</strong></label>`;
                        deleteButton.style.display = 'none';
                    }
                });

                document.getElementById('delete-button').addEventListener('click', function() {
                    const fileInput = document.getElementById('file-input');
                    fileInput.value = '';
                    const dropAreaText = document.getElementById('drop-area-text');
                    dropAreaText.innerHTML = `Glissez & déposez votre image ici ou
                        <label for="file-input" class="drag-uploader__label"><strong>cliquez pour sélectionner</strong></label>`;
                    this.style.display = 'none';
                });
            </script>
        <?php endif; ?>
        <section class="container">
            <h2 class="title">Photos du groupe</h2>
            <?php if (!empty($photos)): ?>
                <div class="photo-gallery">
                    <?php foreach ($photos as $photo): ?>
                        <div class="photo-gallery__item">
                            <img src="/uploads/group_<?= $group['id']; ?>/<?= $photo['filename']; ?>" alt="Photo">
                            <div class="photo-gallery__actions">
                                <?php if (Group::isOwner($_SESSION['user_id'], $group['id']) || $_SESSION['user_id'] == $photo['user_id']): ?>
                                    <form method="POST" action="/photo/delete">
                                        <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                                        <button type="submit" class="photo-gallery__button">❌ Supprimer</button>
                                    </form>
                                <?php endif; ?>
                                <?php if (Group::isOwner($_SESSION['user_id'], $group['id']) || $_SESSION['user_id'] == $photo['user_id']): ?>
                                    <?php if (empty($photo['public_token'])): ?>
                                        <form method="POST" action="/photo/share">
                                            <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                                            <button type="submit" class="photo-gallery__button">🔗 Partager</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" action="/photo/unshare">
                                            <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                                            <button type="submit" class="photo-gallery__button">❌ Supprimer le lien</button>
                                        </form>
                                        <p>🔗 <a href="/photo/<?= $photo['public_token'] ?>" target="_blank">lien public</a></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center">Aucune photo pour le moment.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>Vous n'êtes pas membre de ce groupe.</p>
        <?php endif; ?>
        </section>

        <?php if (GroupMember::isMember($_SESSION['user_id'], $group['id']) && (!Group::isOwner($_SESSION['user_id'], $group['id']) || GroupMember::hasMultipleOwners($group['id']))): ?>
            <form method="POST" action="/group/<?= $group['id'] ?>/leave">
                <button type="submit" class="button button--danger">Quitter le groupe</button>
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