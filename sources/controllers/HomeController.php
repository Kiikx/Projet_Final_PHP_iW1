<?php

class HomeController
{
    public static function index(): void
    {

        $pageTitle = "Accueil";
        $user = null;
    
        if (isset($_SESSION["user_id"])) {
            require_once __DIR__ . "/../models/User.php";
            $user = User::findById($_SESSION["user_id"]);
        }
    
        require_once __DIR__ . "/../views/home/index.php";

    }
}
