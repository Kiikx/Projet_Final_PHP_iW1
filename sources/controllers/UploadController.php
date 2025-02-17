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
        session_start();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['image'])) {
            die("❌ Requête invalide.");
        }

        // Définition des contraintes de sécurité pour l'upload
        $file = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($file['type'], $allowedTypes)) {
            die("❌ Format non autorisé.");
        }

        if ($file['size'] > $maxSize) {
            die("❌ Fichier trop volumineux.");
        }

        // Récupération des infos utilisateur et groupe
        $groupId = $_POST['group_id'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;

        // Vérification du rôle de l'utilisateur
        $userRole = GroupMember::getRole($userId, $groupId);
        if (!in_array($userRole, ['write', 'owner'])) {
            die("❌ Vous n'avez pas la permission d'uploader des images.");
        }

        if (!$groupId || !$userId) {
            die("❌ Le groupe et l'utilisateur sont requis.");
        }

        // Vérifier si l'utilisateur appartient bien au groupe avant l'upload
        if (!GroupMember::isMember($userId, $groupId)) {
            die("❌ Vous ne pouvez pas uploader dans ce groupe.");
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
            die("❌ Erreur lors de l'upload.");
        }
    }

    public static function deletePhoto()
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die("❌ Requête invalide.");
        }

        $photoId = $_POST['photo_id'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;

        if (!$photoId || !$userId) {
            die("❌ L'ID de la photo et l'utilisateur sont requis.");
        }

        // Récupérer l’image en base
        $photo = Photo::getById($photoId);
        if (!$photo) {
            die("❌ Cette image n'existe pas.");
        }

        // Vérifier si l'utilisateur a le droit de supprimer cette image
        if (!Group::isOwner($userId, $photo['group_id']) && $photo['user_id'] != $userId) {
            die("❌ Vous ne pouvez pas supprimer cette image.");
        }

        // Supprimer le fichier du stockage local
        $filePath = __DIR__ . "/../uploads/group_{$photo['group_id']}/{$photo['filename']}";
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Supprimer l’entrée en base de données
        Photo::delete($photoId);

        echo "✅ Image supprimée avec succès.";
    }
}
