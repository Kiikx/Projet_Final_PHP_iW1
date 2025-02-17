<?php
require_once __DIR__ . "/../core/Database.php";
$pdo = Database::getConnection();

// 🔹 Création de la table migrations si elle n'existe pas
$pdo->query("
    CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) UNIQUE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

// 🔹 Récupérer les migrations déjà appliquées
$migrated = $pdo->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);

// 🔹 Appliquer toutes les migrations qui ne sont pas encore exécutées
$migrationsPath = __DIR__ . "/migrations/";
foreach (glob($migrationsPath . "*.php") as $migration) {
    $filename = basename($migration);
    if (!in_array($filename, $migrated)) {
        require_once $migration;
        $pdo->query("INSERT INTO migrations (migration) VALUES ('$filename')");
        echo "✅ Migration appliquée : $filename\n";
    }
}
