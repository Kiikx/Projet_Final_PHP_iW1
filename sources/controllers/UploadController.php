<?php

class UploadController
{
    public static function index()
    {
        require_once __DIR__ . '/../views/upload/index.php';
    }

    public static function post()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
            $file = $_FILES['image'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 2 * 1024 * 1024; // 2MB

            if (!in_array($file['type'], $allowedTypes)) {
                die('Format non autorisé.');
            }

            if ($file['size'] > $maxSize) {
                die('Fichier trop volumineux.');
            }

            $uploadDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $filename = uniqid() . '_' . basename($file['name']);
            $destination = $uploadDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                require_once __DIR__ . '/../models/Photo.php';

                $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Par défaut, user_id = 1

                Photo::save($filename, $userId);

                echo "Upload réussi !";
            } else {
                echo "Erreur lors de l'upload.";
            }
        }
    }
}
