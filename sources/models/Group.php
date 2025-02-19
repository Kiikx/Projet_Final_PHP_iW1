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

    public static function updateName($groupId, $name)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE groups SET name = :name WHERE id = :id");
        return $stmt->execute(['name' => $name, 'id' => $groupId]);
    }

    public static function isOwner($userId, $groupId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id FROM groups WHERE id = :group_id AND owner_id = :user_id");
        $stmt->execute(['group_id' => $groupId, 'user_id' => $userId]);
        return (bool) $stmt->fetch();
    }

    public static function getAll()
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM groups");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getUserGroups($id)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT g.* FROM groups g JOIN group_members gm ON g.id = gm.group_id WHERE gm.user_id = :user_id");
        $stmt->execute(['user_id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($groupId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM groups WHERE id = :id");
        $stmt->execute(['id' => $groupId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
