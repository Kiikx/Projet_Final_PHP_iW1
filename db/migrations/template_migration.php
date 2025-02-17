<?php
/**
 * Template de migration
 * ⚡ Copiez ce fichier et renommez-le : YYYYMMDDHHMM_nom_de_la_migration.php
 * ⚡ Ajoutez ensuite votre requête SQL pour la création ou modification d'une table.
 */

require_once dirname(__DIR__, 2) . "/core/Database.php";
$pdo = Database::getConnection();

if (isset($rollback) && $rollback === true) {
    echo "🔄 Rollback : Annulation des modifications...\n";

    // ❌ Annulez les modifications ici (Ex: DROP TABLE, REMOVE COLUMN, etc.)
    // Exemple : $pdo->query("ALTER TABLE users DROP COLUMN example_column");

} else {
    echo "✅ Application de la migration...\n";

    // 🚀 Ajoutez votre modification ici (Ex: CREATE TABLE, ADD COLUMN, etc.)
    // Exemple : $pdo->query("ALTER TABLE users ADD COLUMN example_column VARCHAR(255) NOT NULL");
}
