<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$rootPath = __DIR__;
chdir($rootPath);    

$config = array();  

require_once $rootPath . '/app/etc/config.php';
require_once $rootPath . '/autoload.php'; 

use core\utils\Router;

$router = new Router();

$uri = trim($_GET['uri'] ?? '', '/');
$module = ($uri === '' || str_starts_with($uri, 'index.php')) ? 'home' : $uri;

$router->dispatch($module);
?>