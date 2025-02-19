<?php
require_once dirname(__DIR__, 2) . "/core/Database.php";
$pdo = Database::getConnection();

if (isset($rollback) && $rollback === true) {
    echo "🔄 Suppression de la colonne public_token...\n";
    $pdo->query("ALTER TABLE photos DROP COLUMN public_token");
} else {
    echo "✅ Ajout de la colonne public_token...\n";
    $pdo->query("ALTER TABLE photos ADD COLUMN public_token VARCHAR(255) NULL UNIQUE");
}
