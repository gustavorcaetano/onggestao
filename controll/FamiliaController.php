<?php
require_once 'model/Familia.php';

class FamiliaController {

    public function __construct() {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
            header("Location: /index.php?url=auth/login");
            exit;
        }
    }

    public function index() {
        header("Location: /index.php?url=admin/dashboard");
        exit;
    }

    public function cadastrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = [
                'nome_responsavel' => $_POST['nome_responsavel'] ?? '',
                'email'            => $_POST['email'] ?? '',
                'cpf'              => $_POST['cpf'] ?? '',
                'telefone'         => $_POST['telefone'] ?? '',
                'quantidade_membros' => $_POST['quantidade_membros'] ?? 0,
                'renda_familiar'   => $_POST['renda_familiar'] ?? 0,
                'endereco'         => $_POST['endereco'] ?? ''
            ];
            Familia::cadastrar($dados);
            header("Location: /index.php?url=admin/dashboard");
            exit;
        }
    }

    public function atualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $dados = [
                'nome_responsavel' => $_POST['nome_responsavel'] ?? '',
                'email'            => $_POST['email'] ?? '',
                'cpf'              => $_POST['cpf'] ?? '',
                'telefone'         => $_POST['telefone'] ?? '',
                'quantidade_membros' => $_POST['quantidade_membros'] ?? 0,
                'renda_familiar'   => $_POST['renda_familiar'] ?? 0,
                'endereco'         => $_POST['endereco'] ?? ''
            ];
            if ($id) {
                Familia::atualizar($id, $dados);
            }
            header("Location: /index.php?url=admin/dashboard");
            exit;
        }
    }

    public function excluir() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $justificativa = $_POST['justificativa'] ?? '';
            if ($id) {
                Familia::excluir($id, $justificativa);
            }
            header("Location: /index.php?url=admin/dashboard");
            exit;
        }
    }
}
?>