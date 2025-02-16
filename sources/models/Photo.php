<?php

require_once __DIR__ . '/../core/Database.php';

class Photo
{
    public static function save($filename, $userId)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO photos (filename, user_id) VALUES (:filename, :user_id)");
        $stmt->execute([
            'filename' => $filename,
            'user_id' => $userId,
        ]);
    }
}
