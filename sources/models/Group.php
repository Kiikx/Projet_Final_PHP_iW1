<?php
require_once __DIR__ . '/../core/Database.php';

class Group
{
    public static function create($name, $ownerId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO groups (name, owner_id) VALUES (:name, :owner_id)");
        $stmt->execute(['name' => $name, 'owner_id' => $ownerId]);
        return $pdo->lastInsertId();
    }

    public static function delete($groupId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM groups WHERE id = :id");
        return $stmt->execute(['id' => $groupId]);
    }

    public static function isOwner($userId, $groupId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id FROM groups WHERE id = :group_id AND owner_id = :user_id");
        $stmt->execute(['group_id' => $groupId, 'user_id' => $userId]);
        return (bool) $stmt->fetch();
    }
}
