<?php

require_once __DIR__ . '/../models/Photo.php';

class UploadController
{
    public static function index()
    {
        require_once __DIR__ . '/../views/upload/index.php';
    }
    public static function post()
    {
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['image'])) {
            header("Location: /groups");
            return;
        }

        // Récupération des infos utilisateur et groupe
        $groupId = $_POST['group_id'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;

        // Définition des contraintes de sécurité pour l'upload
        $file = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($file['type'], $allowedTypes)) {
            header("Location: /groups/$groupId");
            return;
        }

        if ($file['size'] > $maxSize) {
            header("Location: /groups/$groupId");
            return;
        }



        // Vérification du rôle de l'utilisateur
        $userRole = GroupMember::getRole($userId, $groupId);
        if (!in_array($userRole, ['write', 'owner'])) {
            header("Location: /groups/$groupId");
            return;
        }

        if (!$groupId || !$userId) {
            header("Location: /groups/$groupId");
            return;
        }

        // Vérifier si l'utilisateur appartient bien au groupe avant l'upload
        if (!GroupMember::isMember($userId, $groupId)) {
            header("Location: /groups/$groupId");
            return;
        }

        // 🔥 Stockage LOCAL pour l'instant (dans le dossier `uploads/{group_id}/`)
        // 📌 Plus tard, on passera sur un stockage Cloud comme :
        // - Amazon S3 (scalabilité et persistance)
        // - Google Cloud Storage (Google)
        // - Cloudinary (spécialisé dans l'optimisation d'images avec CDN)
        // - Supabase Storage (alternative open-source)
        // plus performant et sécurisé en gros mais plus relou à mettre en place

        // Création d'un dossier spécifique pour chaque groupe
        $uploadDir = __DIR__ . "/../uploads/group_$groupId/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Génération d'un nom de fichier unique : {user_id}_{timestamp}_nomfichier.ext
        $timestamp = time();
        $filename = "{$userId}_{$timestamp}_" . basename($file['name']);
        $destination = $uploadDir . $filename;

        // Déplacement du fichier vers le dossier du groupe
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Enregistrer le fichier dans la BDD
            Photo::save($filename, $userId, $groupId);
            echo "✅ Upload réussi : $filename";
        } else {
            header("Location: /groups/$groupId");
            return;
        }
    }

    public static function deletePhoto()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /groups");
            return;
        }

        $photoId = $_POST['photo_id'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;
        $groupId = $_POST['group_id'] ?? null;


        if (!$photoId || !$userId) {
            header("Location: /groups/$groupId");
            return;
        }

        // Récupérer l’image en base
        $photo = Photo::getById($photoId);
        if (!$photo) {
            header("Location: /groups/$groupId");
            return;
        }

        // Vérifier si l'utilisateur a le droit de supprimer cette image
        if (!Group::isOwner($userId, $photo['group_id']) && $photo['user_id'] != $userId) {
            header("Location: /groups/$groupId");
            return;
        }

        // Supprimer le fichier du stockage local
        $filePath = __DIR__ . "/../uploads/group_{$photo['group_id']}/{$photo['filename']}";
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Supprimer l’entrée en base de données
        Photo::delete($photoId);

        header("Location: /groups/$groupId");
        return;
    }

    public static function viewPublicPhoto($token)
    {
        $photo = Photo::getByToken($token);

        if (!$photo) {
            http_response_code(404);
            header("Location: /groups");
            return;
        }

        header("Content-Type: image/jpeg");
        readfile(__DIR__ . "/../uploads/group_{$photo['group_id']}/{$photo['filename']}");
    }

    public static function sharePhoto()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            return;
        }

        $photoId = $_POST['photo_id'] ?? null;
        if (!$photoId) {
            header("Location: /groups");
            return;
        }

        $photo = Photo::getById($photoId);
        if (!$photo) {
            header("Location: /groups");
            return;
        }

        if ($photo['user_id'] !== $_SESSION['user_id'] && !Group::isOwner($_SESSION['user_id'], $photo['group_id'])) {
            header("Location: /groups");
            return;
        }

        if (!empty($photo['public_token'])) {
            header("Location: /groups");
            return;
        }

        $token = Photo::generatePublicToken($photoId);
        header("Location: /group/{$photo['group_id']}");

        return;
    }

    public static function unsharePhoto()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            return;
        }

        $photoId = $_POST['photo_id'] ?? null;
        if (!$photoId) {
            header("Location: /groups");
            return;
        }

        $photo = Photo::getById($photoId);
        if (!$photo) {
            header("Location: /groups");
            return;
        }

        if ($photo['user_id'] !== $_SESSION['user_id'] && !Group::isOwner($_SESSION['user_id'], $photo['group_id'])) {
            header("Location: /groups");
            return;
        }

        Photo::removePublicToken($photoId);
        header("Location: /group/{$photo['group_id']}");

        return;
    }
}
