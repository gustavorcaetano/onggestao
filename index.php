<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

// Garante o caminho absoluto baseado no diretório atual do script no servidor
$base_path = __DIR__ . '/';

// Garante que os usuários padrão existem no banco em qualquer acesso inicial
require_once $base_path . 'model/Usuario.php';
Usuario::criarAdminPadrao();
Usuario::criarBeneficiarioPadrao();

// Pega a url do parâmetro ?url=... enviado pelo navegador
$uri = isset($_GET['url']) ? $_GET['url'] : 'home';
$uri = rtrim($uri, '/');
$uri = filter_var($uri, FILTER_SANITIZE_URL);
$uri = explode('/', $uri);

$controllerName = ucfirst($uri[0]) . 'Controller';
$action = isset($uri[1]) ? $uri[1] : 'index';

$controllerFile = $base_path . 'controll/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    if (class_exists($controllerName)) {
        $controllerInstance = new $controllerName();
        if (method_exists($controllerInstance, $action)) {
            $params = array_slice($uri, 2);
            call_user_func_array([$controllerInstance, $action], $params);
        } else {
            echo "Ação <b>{$action}</b> não encontrada.";
        }
    } else {
        echo "Controller <b>{$controllerName}</b> não encontrado.";
    }
} else {
    // Se cair aqui ou na home
    if ($uri[0] == 'home' || $uri[0] == '') {
        require_once $base_path . 'controll/HomeController.php';
        $home = new HomeController();
        $home->index();
    } else {
        echo "Página não encontrada.";
    }
}
?>