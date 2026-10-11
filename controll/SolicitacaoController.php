<?php
require_once 'model/Solicitacao.php';

class SolicitacaoController {
    
    public function __construct() {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
            header("Location: /index.php?url=auth/login");
            exit;
        }
    }

    public function index() {
        $solicitacoes = Solicitacao::listarTodas();
        require_once 'view/admin/solicitacoes.php';
    }

    public function alterarStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $status = $_POST['status'] ?? 'pendente';

            if ($id) {
                try {
                    Solicitacao::atualizarStatus($id, $status);
                    header("Location: /index.php?url=solicitacao/index&sucesso=" . urlencode("Status da solicitação atualizado para '$status' com sucesso!"));
                    exit;
                } catch (Exception $e) {
                    header("Location: /index.php?url=solicitacao/index&erro=" . urlencode("Erro ao atualizar o status do pedido. Tente novamente."));
                    exit;
                }
            } else {
                header("Location: /index.php?url=solicitacao/index&erro=" . urlencode("Identificador da solicitação inválido."));
                exit;
            }
        }
    }
}
?>