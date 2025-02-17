<?php
require_once dirname(__DIR__, 2) . "/core/Database.php";
$pdo = Database::getConnection();

if (isset($rollback) && $rollback === true) {
    echo "🔄 Suppression de la table users...\n";
    $pdo->query("DROP TABLE IF EXISTS users");
} else {
    echo "✅ Création de la table users...\n";
    $pdo->query("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
}
