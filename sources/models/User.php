<?php

require_once __DIR__ . "/../core/Database.php";

class User
{
    public int $id;
    public string $username;
    public string $email;
    public string $password;

    public function __construct(array $data)
    {
        $this->id = $data["id"] ?? 0;
        $this->username = $data["username"];
        $this->email = $data["email"];
        $this->password = $data["password"];
    }

    // Vérifier si un email est déjà utilisé
    public static function existsByEmail(string $email): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(["email" => $email]);
        return (bool) $stmt->fetch();
    }

    // Trouver un utilisateur par email
    public static function findOneByEmail(string $email): ?User
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(["email" => $email]);
        $user = $stmt->fetch();

        return $user ? new User($user) : null;
    }

    // Vérifier si un mot de passe est correct
    public function isValidPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    // Créer un utilisateur 
    public static function create(array $data): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password) 
            VALUES (:username, :email, :password)
        ");

        return $stmt->execute([
            "username" => $data["username"],
            "email" => $data["email"],
            "password" => $data["password"]
        ]);
    }
}
