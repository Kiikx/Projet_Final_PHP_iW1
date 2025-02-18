<?php
require_once dirname(__DIR__, 2) . "/core/Database.php";
$pdo = Database::getConnection();

if (isset($rollback) && $rollback === true) {
    echo "🔄 Suppression de la table group_members...\n";
    $pdo->query("DROP TABLE IF EXISTS group_members");
} else {
    echo "✅ Création de la table group_members...\n";
    $pdo->query("
        CREATE TABLE group_members (
            group_id INT NOT NULL,
            user_id INT NOT NULL,
            role ENUM('read', 'write', 'owner') DEFAULT 'read',
            PRIMARY KEY (group_id, user_id),
            FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
}
