<?php
require_once dirname(__DIR__, 2) . "/core/Database.php";
$pdo = Database::getConnection();

if (isset($rollback) && $rollback === true) {
    echo "🔄 Suppression de la table photos...\n";
    $pdo->query("DROP TABLE IF EXISTS photos");
} else {
    echo "✅ Création de la table photos...\n";
    $pdo->query("
        CREATE TABLE photos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            group_id INT NULL,
            filename VARCHAR(255) NOT NULL,
            visibility ENUM('private', 'group', 'public') DEFAULT 'group',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE SET NULL
        )
    ");
}
