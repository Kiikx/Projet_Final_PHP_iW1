<?php

class PasswordRequest
{
    public string $email;
    public string $password;
    public string $password_confirm;
    public string $token;

    public function __construct()
    {
        $this->email = trim($_POST["email"] ?? "");
        $this->password = $_POST["password"] ?? "";
        $this->password_confirm = $_POST["password_confirm"] ?? "";
        $this->token = $_GET["token"] ?? "";
    }

    public function validateForgot(): array
    {
        $errors = [];

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse email est invalide.";
        }

        if (!User::findOneByEmail($this->email)) {
            $errors[] = "Aucun compte trouvé avec cet email.";
        }

        return $errors;
    }

    public function validateReset(): array
    {
        $errors = [];

        if (strlen($this->password) < 6) {
            $errors[] = "Le mot de passe doit faire au moins 6 caractères.";
        }

        if ($this->password !== $this->password_confirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }

        return $errors;
    }
}
