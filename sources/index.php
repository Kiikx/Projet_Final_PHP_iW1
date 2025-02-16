<?php

require_once __DIR__ . "/core/Router.php";

require_once __DIR__ . "/controllers/LoginController.php";
require_once __DIR__ . "/controllers/RegisterController.php";
require_once __DIR__ . "/controllers/ArticleController.php";
require_once __DIR__ . "/controllers/UploadController.php";
require_once __DIR__ . "/controllers/PasswordController.php";

$router = new Router();

// $router->get("/login", LoginController::class, "index");
// $router->post("/login", LoginController::class, "post");

// $router->get("/articles/add/{slug}", ArticleController::class, "index");

// $router->get("/articles/{slug}", ArticleController::class, "index");

// $router->get("/register", RegisterController::class, "index");



$router->get("/register", RegisterController::class, "index");
$router->post("/register", RegisterController::class, "post");

$router->get("/login", LoginController::class, "index");
$router->post("/login", LoginController::class, "post");

$router->get("/upload", UploadController::class, "index"); 
$router->post("/upload", UploadController::class, "post"); 

$router->get("/password/forgot", PasswordController::class, "forgot");
$router->post("/password/forgot", PasswordController::class, "forgot");

$router->get("/password/reset", PasswordController::class, "reset");
$router->post("/password/reset", PasswordController::class, "reset");


$router->start();

require_once __DIR__ . "/core/QueryBuilder.php";
