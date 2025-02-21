<?php

require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../requests/RegisterRequest.php";

class RegisterController
{
    public static function index(): void
    {
        $errors = [];
        require_once __DIR__ . "/../views/register/index.php";
    }

    public static function post(): void
    {
        $request = new RegisterRequest();
        $errors = $request->validate();

        if (!empty($errors)) {
            require_once __DIR__ . "/../views/register/index.php";
            return;
        }

        // Enregistrement de l'utilisateur
        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => password_hash($request->password, PASSWORD_DEFAULT),
        ]);

        header("Location: /login");
        return;
    }
}
