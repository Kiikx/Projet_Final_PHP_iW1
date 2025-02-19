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
            die("❌ Le nom du groupe est obligatoire.");
        }

        $groupId = Group::create($groupName, $_SESSION['user_id']);
        if ($groupId) {
            GroupMember::addMember($groupId, $_SESSION['user_id'], 'owner');
            header("Location: /groups");
            echo "✅ Groupe créé avec succès.";
        } else {
            die("❌ Erreur lors de la création du groupe.");
        }
    }

    /**
     * Supprimer un groupe (seul le propriétaire peut le faire)
     */
    public static function delete($groupId)
    {
        if (!isset($_SESSION['user_id'])) {
            die("❌ Vous devez être connecté.");
        }

        if (!$groupId || !Group::isOwner($_SESSION['user_id'], $groupId)) {
            die("❌ Vous n'avez pas le droit de supprimer ce groupe.");
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
            die("❌ Erreur lors de la suppression.");
        }
    }

    /**
     * Modifier le nom d'un groupe
     */
    public static function update($groupId)
    {
        if (!isset($_SESSION['user_id'])) {
            die("❌ Vous devez être connecté.");
        }

        if (!$groupId || !Group::isOwner($_SESSION['user_id'], $groupId)) {
            die("❌ Vous n'avez pas le droit de modifier ce groupe.");
        }

        $newName = trim($_POST['group_name'] ?? '');
        if (empty($newName)) {
            die("❌ Le nom du groupe est obligatoire.");
        }

        if (Group::updateName($groupId, $newName)) {
            header("Location: /group/$groupId");
            echo "✅ Nom du groupe mis à jour.";
        } else {
            die("❌ Erreur lors de la mise à jour.");
        }
    }

    /**
     * Ajouter un utilisateur à un groupe
     */
    public static function addMember($groupId)
    {
        if (!isset($_SESSION['user_id'])) {
            die("❌ Vous devez être connecté.");
        }

        $email = $_POST['email'] ?? null;
        $role = $_POST['role'] ?? 'read';

        if (!$email) {
            die("❌ Email obligatoire.");
        }

        $group = Group::getById($groupId);
        if (!$group) {
            die("❌ Groupe introuvable.");
        }

        if (!Group::isOwner($_SESSION['user_id'], $groupId)) {
            die("❌ Seul un propriétaire peut ajouter des membres.");
        }

        $user = User::getByEmail($email);
        if (!$user) {
            die("❌ Aucun utilisateur trouvé avec cet email.");
        }

        $userId = $user['id'];

        if (GroupMember::isMember($userId, $groupId)) {
            die("❌ Cet utilisateur est déjà membre du groupe.");
        }

        if (GroupMember::addMember($groupId, $userId, $role)) {
            echo "✅ Membre ajouté avec le rôle : $role";
        } else {
            die("❌ Erreur lors de l'ajout.");
        }
    }

    /**
     * Retirer un utilisateur d'un groupe
     */
    public static function removeMember()
    {
        if (!isset($_SESSION['user_id'])) {
            die("❌ Vous devez être connecté.");
        }

        $groupId = $_POST['group_id'] ?? null;
        $userId = $_POST['user_id'] ?? null;

        if (!$groupId || !$userId || !Group::isOwner($_SESSION['user_id'], $groupId)) {
            die("❌ Vous ne pouvez pas retirer ce membre.");
        }

        if (!GroupMember::isMember($userId, $groupId)) {
            die("❌ Cet utilisateur n'est pas dans le groupe.");
        }

        // Vérifier si l'utilisateur retiré est un owner
        $isOwner = Group::isOwner($userId, $groupId);

        // Si c'est un owner, vérifier qu'il y en a d'autres avant de le supprimer
        if ($isOwner && !GroupMember::hasMultipleOwners($groupId)) {
            die("❌ Impossible de supprimer ce membre : il est le dernier propriétaire du groupe.");
        }


        if (GroupMember::removeMember($groupId, $userId)) {
            echo "✅ Membre retiré.";
        } else {
            die("❌ Erreur lors de la suppression.");
        }
    }

    public static function leaveGroup($groupId)
    {
        if (!isset($_SESSION['user_id'])) {
            die("❌ Vous devez être connecté.");
        }

        $userId = $_SESSION['user_id'];

        // 🔥 Vérifier si le groupe existe
        $group = Group::getById($groupId);
        if (!$group) {
            die("❌ Groupe introuvable.");
        }

        if (!GroupMember::isMember($userId, $groupId)) {
            die("❌ Vous ne faites pas partie de ce groupe.");
        }

        if (Group::isOwner($userId, $groupId)) {
            if (!GroupMember::hasMultipleOwners($groupId)) {
                die("❌ Vous êtes le dernier propriétaire. Transférez la propriété avant de quitter.");
            }
        }

        if (GroupMember::removeMember($groupId, $userId)) {
            echo "✅ Vous avez quitté le groupe.";
        } else {
            die("❌ Erreur lors du départ.");
        }
    }


    public static function changeRole()
    {
        if (!isset($_SESSION['user_id'])) {
            die("❌ Vous devez être connecté.");
        }

        $groupId = $_POST['group_id'] ?? null;
        $userId = $_POST['user_id'] ?? null;
        $newRole = $_POST['role'] ?? null;

        if (!$groupId || !$userId || !$newRole || !in_array($newRole, ['read', 'write', 'owner'])) {
            die("❌ Rôle invalide.");
        }

        // Seul un `owner` peut changer les rôles
        if (!Group::isOwner($_SESSION['user_id'], $groupId)) {
            die("❌ Seul un propriétaire peut modifier les rôles.");
        }

        // Vérifier qu'on ne supprime pas le dernier `owner`
        if ($newRole !== 'owner' && Group::isOwner($userId, $groupId) && !GroupMember::hasMultipleOwners($groupId)) {
            die("❌ Impossible de rétrograder ce membre : il est le dernier propriétaire.");
        }

        if (GroupMember::updateRole($groupId, $userId, $newRole)) {
            echo "✅ Rôle mis à jour.";
        } else {
            die("❌ Erreur lors de la modification du rôle.");
        }
    }
    public static function index()
    {
        if (!isset($_SESSION['user_id'])) {
            die("❌ Vous devez être connecté pour voir les groupes.");
        }


        require_once __DIR__ . '/../models/Group.php';
        $groups = Group::getUserGroups($_SESSION['user_id']);

        require_once __DIR__ . '/../views/group/index.php';
    }

    public static function show($groupId)
    {

        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            die("❌ Vous devez être connecté pour voir ce groupe.");
        }

        require_once __DIR__ . '/../models/Group.php';
        require_once __DIR__ . '/../models/GroupMember.php';
        require_once __DIR__ . '/../models/Photo.php';

        $group = Group::getById($groupId);
        if (!$group) {
            die("❌ Ce groupe n'existe pas.");
        }

        $isMember = GroupMember::isMember($_SESSION['user_id'], $groupId);
        $photos = Photo::getByGroup($groupId);

        require_once __DIR__ . '/../views/group/show.php';
    }
}
