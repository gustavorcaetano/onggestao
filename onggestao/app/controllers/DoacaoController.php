<?php

use core\database\DBConnection;

global $rootPath;
if (file_exists($rootPath . '/app/models/Doacao.php')) {
    require_once $rootPath . '/app/models/Doacao.php';
}

class DoacaoController {
    
    public function __construct() {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
            header("Location: " . $_SESSION['url']['root'] . "auth/login");
            exit;
        }
    }

    public function index() {
        global $rootPath;

        $doacoes = class_exists('Doacao') ? Doacao::listarTodas() : [];
        
        try {
            $db = (new DBConnection())->getConn();
            $beneficiariosPendentes = $db->query("SELECT * FROM usuarios WHERE tipo = 'beneficiario' AND (status = 'pendente' OR status = 'Em Análise') ORDER BY id DESC")->fetchAll();
            $totalPendentes = count($beneficiariosPendentes);
            $pedidosPendentesCount = $db->query("SELECT COUNT(*) FROM solicitacoes WHERE status = 'Pendente'")->fetchColumn();
        } catch (Exception $e) {
            $beneficiariosPendentes = [];
            $totalPendentes = 0;
            $pedidosPendentesCount = 0;
        }
        try {
            $db = (new DBConnection())->getConn();
            $beneficiariosPendentes = $db->query("SELECT * FROM usuarios WHERE tipo = 'beneficiario' AND (status = 'pendente' OR status = 'Em Análise') ORDER BY id DESC")->fetchAll();
            $totalPendentes = count($beneficiariosPendentes);
        } catch (Exception $e) {
            $beneficiariosPendentes = [];
            $totalPendentes = 0;
        }
        
        $viewPath = $rootPath . '/app/views/admin/doacoes.php';
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            require_once $rootPath . '/app/views/admin/dashboard.php';
        }
    }

    public function cadastrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = [
                'doador_nome'         => $_POST['doador_nome'] ?? 'Anônimo',
                'tipo_doacao'         => $_POST['tipo_doacao'] ?? 'Alimento',
                'quantidade_ou_valor' => $_POST['quantidade_ou_valor'] ?? '',
                'descricao'           => $_POST['descricao'] ?? ''
            ];

            if (class_exists('Doacao')) {
                Doacao::cadastrar($dados);
            }
            
            header("Location: " . $_SESSION['url']['root'] . "doacao/index");
            exit;
        }
    }
}

$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$controllerInstance = new DoacaoController();

if (strpos($requestUri, 'doacao/cadastrar') !== false) {
    $controllerInstance->cadastrar();
} else {
    $controllerInstance->index();
}
?>