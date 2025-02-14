<?php

class LoginRequest
{
    public string $email;
    public string $password;

    public function __construct()
    {
        $this->email = strtolower(trim(htmlspecialchars($_POST["email"] ?? "")));
        $this->password = $_POST["password"] ?? "";
    }

    public function validate(): array
    {
        $errors = [];

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse email est invalide.";
        }

        if (empty($this->password)) {
            $errors[] = "Le mot de passe est obligatoire.";
        }

        return $errors;
    }
}
