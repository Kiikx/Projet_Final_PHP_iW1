<?php

require_once __DIR__ . "/../models/User.php";

class RegisterRequest
{
    public string $username;
    public string $email;
    public string $password;
    public string $password_confirm;

    public function __construct()
    {
        $this->username = trim(htmlspecialchars($_POST["username"] ?? ""));
        $this->email = strtolower(trim(htmlspecialchars($_POST["email"] ?? "")));
        $this->password = $_POST["password"] ?? "";
        $this->password_confirm = $_POST["password_confirm"] ?? "";
    }

    public function validate(): array
    {
        $errors = [];

        if (empty($this->username) || strlen($this->username) < 3) {
            $errors[] = "Le nom d'utilisateur doit faire au moins 3 caractères.";
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse email est invalide.";
        } elseif (User::existsByEmail($this->email)) {
            $errors[] = "L'email est déjà utilisé.";
        }

        if (strlen($this->password) < 6) {
            $errors[] = "Le mot de passe doit faire au moins 6 caractères.";
        }

        if ($this->password !== $this->password_confirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }

        return $errors;
    }
}
