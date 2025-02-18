<?php
require_once dirname(__DIR__) . "/core/Database.php";
$pdo = Database::getConnection();

// Récupérer le nombre de migrations à rollback (par défaut = 1)
$iterations = $argv[1] ?? 1;
$iterations = (int) $iterations;

for ($i = 0; $i < $iterations; $i++) {
    // 🔹 Trouver la dernière migration appliquée
    $lastMigration = $pdo->query("SELECT migration FROM migrations ORDER BY id DESC LIMIT 1")->fetchColumn();

    if (!$lastMigration) {
        echo "⚠️ Aucune migration restante à rollback.\n";
        break;
    }

    echo "🔄 Rollback de la migration : $lastMigration\n";

    // 🔹 Vérifier si le fichier de migration existe
    $migrationFile = __DIR__ . "/migrations/" . $lastMigration;
    if (!file_exists($migrationFile)) {
        echo "❌ Le fichier de migration n'existe pas : $migrationFile\n";
        continue;
    }

    // 🔹 Exécuter le rollback en passant `$rollback = true`
    $rollback = true;
    require_once $migrationFile;

    // 🔹 Supprimer la migration de la table `migrations`
    $pdo->query("DELETE FROM migrations WHERE migration = '$lastMigration'");

    echo "✅ Rollback terminé : $lastMigration supprimée.\n";
}
