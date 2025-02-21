<?php
session_start();

require_once __DIR__ . "/core/Router.php";
require_once __DIR__ . "/controllers/HomeController.php";
require_once __DIR__ . "/controllers/LoginController.php";
require_once __DIR__ . "/controllers/RegisterController.php";
require_once __DIR__ . "/controllers/ArticleController.php";
require_once __DIR__ . "/controllers/UploadController.php";
require_once __DIR__ . "/controllers/PasswordController.php";
require_once __DIR__ . "/controllers/GroupController.php";
// require_once __DIR__ . "/controllers/SettingsController.php";


require_once __DIR__ . "/core/QueryBuilder.php";




$router = new Router();
// HOME
$router->get("/", HomeController::class, "index");

// USERS
$router->get("/register", RegisterController::class, "index");
$router->post("/register", RegisterController::class, "post");

$router->get("/login", LoginController::class, "index");
$router->post("/login", LoginController::class, "post");


$router->get("/password/forgot", PasswordController::class, "forgot");
$router->post("/password/forgot", PasswordController::class, "forgot");

$router->get("/password/reset", PasswordController::class, "reset");
$router->post("/password/reset", PasswordController::class, "reset");

$router->get("/logout", LoginController::class, "logout");


// PHOTOS
$router->get("/upload", UploadController::class, "index"); 
$router->post("/upload", UploadController::class, "post"); 
$router->post("/photo/delete", UploadController::class, "deletePhoto");
$router->get("/photo/{token}", UploadController::class, "viewPublicPhoto");
$router->post("/photo/share", UploadController::class, "sharePhoto");
$router->post("/photo/unshare", UploadController::class, "unsharePhoto");

// GROUPS
$router->post("/group/create", GroupController::class, "create");
$router->post("/group/delete/{slug}", GroupController::class, "delete");
$router->post("/group/update/{slug}", GroupController::class, "update");
$router->post("/group/{slug}/add-member", GroupController::class, "addMember");
$router->post("/group/remove-member", GroupController::class, "removeMember");
$router->post("/group/{slug}/leave", GroupController::class, "leaveGroup");
$router->post("/group/change-role", GroupController::class, "changeRole");

$router->get("/groups", GroupController::class, "index");
$router->get("/group/{slug}", GroupController::class, "show");

// SETTINGS
// $router->get("/settings", SettingsController::class, "index");


$router->start();

