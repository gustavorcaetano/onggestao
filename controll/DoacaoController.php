<?php
require_once 'model/Doacao.php';

class DoacaoController {
    
    public function __construct() {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
            header("Location: /index.php?url=auth/login");
            exit;
        }
    }

    public function index() {
        $doacoes = Doacao::listarTodas();
        require_once 'view/admin/doacoes.php';
    }

    public function cadastrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = [
                'doador_nome' => $_POST['doador_nome'] ?? 'Anônimo',
                'tipo_doacao' => $_POST['tipo_doacao'] ?? 'Alimento',
                'quantidade_ou_valor' => $_POST['quantidade_ou_valor'] ?? '',
                'descricao' => $_POST['descricao'] ?? ''
            ];

            Doacao::cadastrar($dados);
            header("Location: /index.php?url=doacao/index");
            exit;
        }
    }
}
?>