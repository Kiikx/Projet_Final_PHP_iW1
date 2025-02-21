<?php
require_once __DIR__ . '/../models/Group.php';
require_once __DIR__ . '/../models/GroupMember.php';

class GroupController
{
    /**
     * Créer un nouveau groupe
     */
    public static function create()
    {
        $errors = [];

        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            die("❌ Vous devez être connecté pour créer un groupe.");
        }

        $groupName = trim($_POST['name'] ?? '');
        if (empty($groupName)) {
            
            header("Location: /groups");
            return;

        }

        $groupId = Group::create($groupName, $_SESSION['user_id']);
        if ($groupId) {
            GroupMember::addMember($groupId, $_SESSION['user_id'], 'owner');
            header("Location: /groups");
            echo "✅ Groupe créé avec succès.";
        } else {
            header("Location: /groups");
            return;
        }
    }

    /**
     * Supprimer un groupe (seul le propriétaire peut le faire)
     */
    public static function delete($groupId)
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            return;
        }

        if (!$groupId || !Group::isOwner($_SESSION['user_id'], $groupId)) {
            header("Location: /groups");
            return;
        }

        $photos = Photo::getByGroup($groupId);
        foreach ($photos as $photo) {
            $filePath = __DIR__ . "/../uploads/group_{$groupId}/{$photo['filename']}";
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            Photo::delete($photo['id']);
        }

        $uploadDir = __DIR__ . "/../uploads/group_{$groupId}/";
        if (is_dir($uploadDir)) {
            rmdir($uploadDir);
        }

        if (Group::delete($groupId)) {
            header("Location: /groups");
            echo "✅ Groupe supprimé.";
        } else {
            header("Location: /groups");
            return;
        }
    }

    /**
     * Modifier le nom d'un groupe
     */
    public static function update($groupId)
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            return;
        }

        if (!$groupId || !Group::isOwner($_SESSION['user_id'], $groupId)) {
            header("Location: /groups");
            error_log("❌ Vous n'êtes pas autorisé à modifier ce groupe.");
            return;
        }

        $newName = trim($_POST['group_name'] ?? '');
        if (empty($newName)) {
            header("Location: /groups");
            error_log("❌ Nom du groupe vide.");
            return;
        }

        if (Group::updateName($groupId, $newName)) {
            header("Location: /groups");
            error_log("✅ Nom du groupe mis à jour.");
        } else {
            header("Location: /groups");
            error_log("❌ Erreur lors de la mise à jour du nom du groupe.");
            return;
        }
    }

    /**
     * Ajouter un utilisateur à un groupe
     */
    public static function addMember($groupId)
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /group/$groupId");
            return;
        }

        $email = $_POST['email'] ?? null;
        $role = $_POST['role'] ?? 'read';

        if (!$email) {
            header("Location: /group/$groupId");
            return;
        }

        $group = Group::getById($groupId);
        if (!$group) {
            header("Location: /groups");
            return;
        }

        if (!Group::isOwner($_SESSION['user_id'], $groupId)) {
            header("Location: /group/$groupId");
            return;
        }

        $user = User::getByEmail($email);
        if (!$user) {
            header("Location: /group/$groupId");
            return;
        }

        $userId = $user['id'];

        if (GroupMember::isMember($userId, $groupId)) {
            header("Location: /group/$groupId");
            return;
        }

        if (GroupMember::addMember($groupId, $userId, $role)) {
            echo "✅ Membre ajouté avec le rôle : $role";
        } else {
            header("Location: /group/$groupId");
            return;
        }
    }

    /**
     * Retirer un utilisateur d'un groupe
     */
    public static function removeMember()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            return;
        }

        $groupId = $_POST['group_id'] ?? null;
        $userId = $_POST['user_id'] ?? null;

        if (!$groupId || !$userId || !Group::isOwner($_SESSION['user_id'], $groupId)) {
            header("Location: /group/$groupId");
            return;
        }

        if (!GroupMember::isMember($userId, $groupId)) {
            header("Location: /group/$groupId");
            return;
        }

        // Vérifier si l'utilisateur retiré est un owner
        $isOwner = Group::isOwner($userId, $groupId);

        // Si c'est un owner, vérifier qu'il y en a d'autres avant de le supprimer
        if ($isOwner && !GroupMember::hasMultipleOwners($groupId)) {
            header("Location: /group/$groupId");
            return;
        }


        if (GroupMember::removeMember($groupId, $userId)) {
            echo "✅ Membre retiré.";
        } else {
            header("Location: /group/$groupId");
            return;
        }
    }

    public static function leaveGroup($groupId)
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            return;
        }

        $userId = $_SESSION['user_id'];

        // 🔥 Vérifier si le groupe existe
        $group = Group::getById($groupId);
        if (!$group) {
            header("Location: /groups");
            return;
        }

        if (!GroupMember::isMember($userId, $groupId)) {
            header("Location: /groups");
            return;
        }

        if (Group::isOwner($userId, $groupId)) {
            if (!GroupMember::hasMultipleOwners($groupId)) {
                header("Location: /group/$groupId");
            return;
            }
        }

        if (GroupMember::removeMember($groupId, $userId)) {
            echo "✅ Vous avez quitté le groupe.";
        } else {
            header("Location: /group/$groupId");
            return;
        }
    }


    public static function changeRole()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            return;
        }

        $groupId = $_POST['group_id'] ?? null;
        $userId = $_POST['user_id'] ?? null;
        $newRole = $_POST['role'] ?? null;

        if (!$groupId || !$userId || !$newRole || !in_array($newRole, ['read', 'write', 'owner'])) {
            header("Location: /group/$groupId");
            return;
        }

        // Seul un `owner` peut changer les rôles
        if (!Group::isOwner($_SESSION['user_id'], $groupId)) {
            header("Location: /group/$groupId");
            return;
        }

        // Vérifier qu'on ne supprime pas le dernier `owner`
        if ($newRole !== 'owner' && Group::isOwner($userId, $groupId) && !GroupMember::hasMultipleOwners($groupId)) {
            header("Location: /group/$groupId");
            return;
        }

        if (GroupMember::updateRole($groupId, $userId, $newRole)) {
            echo "✅ Rôle mis à jour.";
        } else {
            header("Location: /group/$groupId");
            return;
        }
    }
    public static function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            return;
        }


        require_once __DIR__ . '/../models/Group.php';
        $groups = Group::getUserGroups($_SESSION['user_id']);

        require_once __DIR__ . '/../views/group/index.php';
    }

    public static function show($groupId)
    {

        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            return;
        }

        require_once __DIR__ . '/../models/Group.php';
        require_once __DIR__ . '/../models/GroupMember.php';
        require_once __DIR__ . '/../models/Photo.php';

        $group = Group::getById($groupId);
        if (!$group) {
            header("Location: /groups");
            return;
        }

        $isMember = GroupMember::isMember($_SESSION['user_id'], $groupId);
        $photos = Photo::getByGroup($groupId);

        require_once __DIR__ . '/../views/group/show.php';
    }
}
