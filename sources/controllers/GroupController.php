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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            die("❌ Vous devez être connecté pour créer un groupe.");
        }

        $groupName = trim($_POST['name'] ?? '');
        if (empty($groupName)) {
            die("❌ Le nom du groupe est obligatoire.");
        }

        $groupId = Group::create($groupName, $_SESSION['user_id']);
        if ($groupId) {
            GroupMember::addMember($groupId, $_SESSION['user_id'], 'owner');
            echo "✅ Groupe créé avec succès.";
        } else {
            die("❌ Erreur lors de la création du groupe.");
        }
    }

    /**
     * Supprimer un groupe (seul le propriétaire peut le faire)
     */
    public static function delete()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            die("❌ Vous devez être connecté.");
        }

        $groupId = $_POST['group_id'] ?? null;
        if (!$groupId || !Group::isOwner($_SESSION['user_id'], $groupId)) {
            die("❌ Vous n'avez pas le droit de supprimer ce groupe.");
        }

        if (Group::delete($groupId)) {
            echo "✅ Groupe supprimé.";
        } else {
            die("❌ Erreur lors de la suppression.");
        }
    }

    /**
     * Ajouter un utilisateur à un groupe
     */
    public static function addMember()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            die("❌ Vous devez être connecté.");
        }

        $groupId = $_POST['group_id'] ?? null;
        $userId = $_POST['user_id'] ?? null;
        $role = $_POST['role'] ?? 'read';

        if (!$groupId || !$userId || !Group::isOwner($_SESSION['user_id'], $groupId)) {
            die("❌ Vous ne pouvez pas ajouter un membre car vous n'êtes pas propriétaire du groupe.");
        }

        if (GroupMember::isMember($userId, $groupId)) {
            die("❌ Cet utilisateur est déjà membre du groupe.");
        }

        if (GroupMember::addMember($groupId, $userId, $role)) {
            echo "✅ Membre ajouté.";
        } else {
            die("❌ Erreur lors de l'ajout.");
        }
    }

    /**
     * Retirer un utilisateur d'un groupe
     */
    public static function removeMember()
    {
        session_start();
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

    public static function changeRole()
{
    session_start();
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

}