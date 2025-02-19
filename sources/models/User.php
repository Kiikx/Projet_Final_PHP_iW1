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

    // Mettre a jour le mot de passe
    public static function updatePassword(int $user_id, string $password): void
    {

        $pdo = Database::getConnection();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->execute(["password" => $hashedPassword, "id" => $user_id]);
    }

    public static function findById(int $id): ?User
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(["id" => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ? new User($user) : null;
    }

    public static function getByEmail($email)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
