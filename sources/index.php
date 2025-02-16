<?php

require_once __DIR__ . "/core/Router.php";

require_once __DIR__ . "/controllers/LoginController.php";
require_once __DIR__ . "/controllers/RegisterController.php";
require_once __DIR__ . "/controllers/ArticleController.php";
require_once __DIR__ . "/controllers/UploadController.php";


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


$router->start();

require_once __DIR__ . "/core/QueryBuilder.php";
