<?php

use core\database\DBConnection;

global $rootPath;

class SolicitacaoController {

    public function index() {
        global $rootPath;
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
            header("Location: " . $_SESSION['url']['root'] . "auth/login");
            exit;
        }

        try {
            $db = (new DBConnection())->getConn();
            $solicitacoes = $db->query("SELECT * FROM solicitacoes ORDER BY id DESC")->fetchAll();
            $pedidosPendentesCount = $db->query("SELECT COUNT(*) FROM solicitacoes WHERE status = 'Pendente'")->fetchColumn();
        } catch (Exception $e) {
            $solicitacoes = [];
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
        

        require_once $rootPath . '/app/views/admin/solicitacoes.php';
    }

   public function alterarStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id         = $_POST['id'] ?? '';
            $acao       = $_POST['status'] ?? 'Pendente';
            $observacao = trim($_POST['observacao_admin'] ?? '');

            // Mapeia a ação selecionada para o status correspondente
            $statusMap = [
                'Pendente'  => 'Pendente',
                'Aprovado'  => 'Aprovado',
                'Entregue'  => 'Entregue',
                'Cancelado' => 'Cancelado'
            ];

            $novoStatus = $statusMap[$acao] ?? 'Pendente';

            try {
                $db = (new DBConnection())->getConn();
                $stmt = $db->prepare("UPDATE solicitacoes SET status = :status, observacao_admin = :obs WHERE id = :id");
                $stmt->execute([
                    ':status' => $novoStatus,
                    ':obs'    => $observacao !== '' ? $observacao : null,
                    ':id'     => $id
                ]);

                header("Location: " . $_SESSION['url']['root'] . "solicitacao/index&sucesso=" . urlencode("Status atualizado para '$novoStatus' com sucesso!"));
                exit;
            } catch (Exception $e) {
                header("Location: " . $_SESSION['url']['root'] . "solicitacao/index&erro=" . urlencode("Erro ao atualizar o status."));
                exit;
            }
        }
    }
}

$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$controller = new SolicitacaoController();

if (strpos($requestUri, 'solicitacao/alterarStatus') !== false) {
    $controller->alterarStatus();
} else {
    $controller->index();
}
?>