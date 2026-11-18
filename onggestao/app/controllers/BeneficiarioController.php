<?php

use core\database\DBConnection;

global $rootPath;

class BeneficiarioController {

    public function __construct() {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'beneficiario') {
            header("Location: " . $_SESSION['url']['root'] . "auth/login");
            exit;
        }
    }

   public function painel() {
        global $rootPath;
        $usuario_id = $_SESSION['usuario_id'];
        
        try {
            $db = (new DBConnection())->getConn();
            $usuarioId = $_SESSION['usuario_id'];
        
            // Busca as solicitações do beneficiário (que você já tem)
            $stmtSol = $db->prepare("SELECT * FROM solicitacoes WHERE usuario_id = :uid ORDER BY id DESC");
            $stmtSol->execute([':uid' => $usuarioId]);
            $solicitacoes = $stmtSol->fetchAll();
        
            // NOVO: Busca os dados cadastrais da família do beneficiário
            $stmtFam = $db->prepare("
                SELECT f.*, u.email AS email_login, u.criado_em AS data_cadastro 
                FROM familias f
                INNER JOIN usuarios u ON f.responsavel_id = u.id
                WHERE f.responsavel_id = :uid 
                LIMIT 1
            ");
            $stmtFam->execute([':uid' => $usuarioId]);
            $dadosFamilia = $stmtFam->fetch();
            
            // Busca as ações e campanhas cadastradas pela ONG
            $stmtAcoes = $db->query("SELECT * FROM acoes ORDER BY id DESC LIMIT 6");
            $acoesOng = $stmtAcoes->fetchAll();
        
        } catch (Exception $e) {
            $solicitacoes = [];
            $dadosFamilia = null;
            
            // Não gera avisos caso não haja registros
            $acoesOng = [];
        }

        if (file_exists($rootPath . '/app/views/beneficiario/painel.php')) {
            require_once $rootPath . '/app/views/beneficiario/painel.php';
        } else {
            echo "Painel do Beneficiário carregado com sucesso!";
        }
    }

    public function solicitar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario_id = $_SESSION['usuario_id'] ?? null;
            $nome_usuario = $_SESSION['usuario_nome'] ?? 'Assistido';
            
            try {
                $db = (new DBConnection())->getConn();

                // Insere utilizando exclusivamente as colunas presentes na tabela solicitacoes da imagem 32
                $stmtIns = $db->prepare("INSERT INTO solicitacoes (usuario_id, solicitante_nome, categoria, descricao, status, criado_em) VALUES (:uid, :nome, :cat, :desc, 'Pendente', NOW())");
                $stmtIns->execute([
                    ':uid'   => $usuario_id,
                    ':nome'  => $nome_usuario,
                    ':cat'   => $_POST['categoria'] ?? 'Cesta Básica',
                    ':desc'  => $_POST['descricao'] ?? ''
                ]);
            } catch (Exception $e) {
                // Log de erro se necessário
            }

            header("Location: " . $_SESSION['url']['root'] . "beneficiario/painel&sucesso=" . urlencode("Solicitação enviada com sucesso!"));
            exit;
        }
    }
    
    public function cancelarSolicitacao() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id        = $_POST['id'] ?? '';
            $usuarioId = $_SESSION['usuario_id'] ?? '';

            if (empty($id) || empty($usuarioId)) {
                header("Location: " . $_SESSION['url']['root'] . "beneficiario/painel");
                exit;
            }

            try {
                $db = (new DBConnection())->getConn();

                // Só cancela se o pedido pertencer ao usuário logado E ainda estiver 'Pendente'
                $stmt = $db->prepare("
                    UPDATE solicitacoes 
                    SET status = 'Cancelado', observacao_admin = 'Cancelado pelo próprio beneficiário.' 
                    WHERE id = :id AND usuario_id = :uid AND status = 'Pendente'
                ");
                $stmt->execute([
                    ':id'  => $id,
                    ':uid' => $usuarioId
                ]);

                if ($stmt->rowCount() > 0) {
                    header("Location: " . $_SESSION['url']['root'] . "beneficiario/painel&sucesso=" . urlencode("Solicitação cancelada com sucesso."));
                } else {
                    header("Location: " . $_SESSION['url']['root'] . "beneficiario/painel&erro=" . urlencode("Não foi possível cancelar esta solicitação (ela já pode ter sido analisada)."));
                }
                exit;
            } catch (Exception $e) {
                header("Location: " . $_SESSION['url']['root'] . "beneficiario/painel&erro=" . urlencode("Erro ao processar o cancelamento."));
                exit;
            }
        }
    }
}

$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$controller = new BeneficiarioController();

if (strpos($requestUri, 'beneficiario/solicitar') !== false) {
    $controller->solicitar();
} elseif (strpos($requestUri, 'beneficiario/cancelarSolicitacao') !== false) {
    $controller->cancelarSolicitacao();
} else {
    $controller->painel();
}
?>