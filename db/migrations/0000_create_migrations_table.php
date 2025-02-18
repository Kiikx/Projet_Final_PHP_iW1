<?php
require_once dirname(__DIR__, 2) . "/core/Database.php";

$pdo = Database::getConnection();
$pdo->query("CREATE TABLE IF NOT EXISTS migrations (id INT AUTO_INCREMENT PRIMARY KEY, migration VARCHAR(255) UNIQUE NOT NULL)");
