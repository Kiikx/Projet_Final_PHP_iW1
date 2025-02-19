<?php

require_once __DIR__ . '/../core/Database.php';

class Photo
{
    public static function save($filename, $userId, $groupId)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO photos (filename, user_id, group_id) VALUES (:filename, :user_id, :group_id)");
        $stmt->execute([
            'filename' => $filename,
            'user_id' => $userId,
            'group_id' => $groupId
        ]);
    }

    public static function getById($photoId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM photos WHERE id = :photo_id");
        $stmt->execute(['photo_id' => $photoId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function delete($photoId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM photos WHERE id = :photo_id");
        return $stmt->execute(['photo_id' => $photoId]);
    }

    public static function getByGroup($groupId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM photos WHERE group_id = :group_id");
        $stmt->execute(['group_id' => $groupId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function generatePublicToken($photoId)
    {
        $pdo = Database::getConnection();
        $token = bin2hex(random_bytes(16)); // Générer un token unique de 32 caractères
        $stmt = $pdo->prepare("UPDATE photos SET public_token = :token WHERE id = :photo_id");
        $stmt->execute([
            "token" => $token,
            "photo_id" => $photoId
        ]);
        return $token;
    }

    public static function getByToken($token)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM photos WHERE public_token = :token");
        $stmt->execute(["token" => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function removePublicToken($photoId)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE photos SET public_token = NULL WHERE id = :photo_id");
        return $stmt->execute(['photo_id' => $photoId]);
    }
}
