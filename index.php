<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

// Garante que os usuários padrão existem no banco em qualquer acesso inicial
require_once 'model/Usuario.php';
Usuario::criarAdminPadrao();
Usuario::criarBeneficiarioPadrao();

// Pega a URL do parâmetro ?url=... enviado pelo navegador
$url = isset($_GET['url']) ? $_GET['url'] : 'home';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

$controllerName = ucfirst($url[0]) . 'Controller';
$action = isset($url[1]) ? $url[1] : 'index';

$controllerFile = 'controll/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    if (class_exists($controllerName)) {
        $controllerInstance = new $controllerName();
        if (method_exists($controllerInstance, $action)) {
            $params = array_slice($url, 2);
            call_user_func_array([$controllerInstance, $action], $params);
        } else {
            echo "Ação <b>{$action}</b> não encontrada.";
        }
    } else {
        echo "Controller <b>{$controllerName}</b> não encontrado.";
    }
} else {
    // Se cair aqui ou na home
    if ($url[0] == 'home' || $url[0] == '') {
        require_once 'controll/HomeController.php';
        $home = new HomeController();
        $home->index();
    } else {
        echo "Página não encontrada.";
    }
}
?>