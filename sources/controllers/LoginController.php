<?php
session_start();
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../requests/LoginRequest.php";

class LoginController
{
    public static function index(): void
    {
        $errors = [];
        require_once __DIR__ . "/../views/login/index.php";
    }

    public static function post(): void
    {

        $request = new LoginRequest();
        $errors = $request->validate();

        if (!empty($errors)) {
            require_once __DIR__ . "/../views/login/index.php";
            return;
        }

        $user = User::findOneByEmail($request->email);

        if (!$user || !$user->isValidPassword($request->password)) {
            $errors[] = "Email ou mot de passe incorrect.";
            require_once __DIR__ . "/../views/login/index.php";
            return;
        }

        // Authentification réussie, on stocke l'utilisateur en session
        $_SESSION["user_id"] = $user->id;
        $_SESSION["username"] = $user->username;

        echo "Connexion réussie !";
    }
}
