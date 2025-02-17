<?php
require_once __DIR__ . '/../core/Database.php';

class GroupMember
{
    public static function addMember($groupId, $userId, $role = 'read')
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id, role) VALUES (:group_id, :user_id, :role)");
        return $stmt->execute(['group_id' => $groupId, 'user_id' => $userId, 'role' => $role]);
    }

    public static function removeMember($groupId, $userId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM group_members WHERE group_id = :group_id AND user_id = :user_id");
        return $stmt->execute(['group_id' => $groupId, 'user_id' => $userId]);
    }

    public static function isMember($userId, $groupId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT role FROM group_members WHERE group_id = :group_id AND user_id = :user_id");
        $stmt->execute(['group_id' => $groupId, 'user_id' => $userId]);
        return (bool) $stmt->fetch();
    }

    public static function hasMultipleOwners($groupId)
{
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM group_members WHERE group_id = :group_id AND role = 'owner'");
    $stmt->execute(['group_id' => $groupId]);
    return $stmt->fetchColumn() > 1;
}

public static function updateRole($groupId, $userId, $newRole)
{
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("UPDATE group_members SET role = :role WHERE group_id = :group_id AND user_id = :user_id");
    return $stmt->execute(['role' => $newRole, 'group_id' => $groupId, 'user_id' => $userId]);
}

}
