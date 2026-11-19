<?php
// Arquivo: onggestao/app/controllers/SolicitacaoController.php
use core\database\DBConnection;
// Classe SolicitacaoController gerencia as solicitações feitas pelos beneficiários, permitindo que o administrador visualize e altere o status das solicitações cadastradas no sistema.
global $rootPath;
// Verifica se o arquivo de modelo Solicitacao.php existe antes de incluí-lo, garantindo que a classe Solicitacao esteja disponível para uso no controlador.
class SolicitacaoController {

    // Construtor que verifica se o usuário está logado e é do tipo admin. Caso contrário, redireciona para a página de login.
    public function index() {
        global $rootPath;
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
            header("Location: " . $_SESSION['url']['root'] . "auth/login");
            exit;
        }

        // Tenta buscar as solicitações cadastradas no banco de dados, tratando possíveis exceções e garantindo que a página seja carregada corretamente mesmo que não haja registros.
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
            // Busca os beneficiários pendentes no banco de dados, filtrando por tipo e status, e contando o total de registros encontrados.
            $beneficiariosPendentes = $db->query("SELECT * FROM usuarios WHERE tipo = 'beneficiario' AND (status = 'pendente' OR status = 'Em Análise') ORDER BY id DESC")->fetchAll();
            $totalPendentes = count($beneficiariosPendentes);
        } catch (Exception $e) {
            $beneficiariosPendentes = [];
            $totalPendentes = 0;
        }
        

        require_once $rootPath . '/app/views/admin/solicitacoes.php';
    }

    // Método para alterar o status de uma solicitação, permitindo que o administrador atualize o status e adicione observações.
   public function alterarStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id         = $_POST['id'] ?? '';
            $acao       = $_POST['status'] ?? 'Pendente';
            // Permite que o administrador adicione uma observação ao alterar o status da solicitação, armazenando a informação no banco de dados para referência futura.
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
                // Prepara a consulta SQL para atualizar o status da solicitação no banco de dados, incluindo a observação do administrador, garantindo que apenas o registro correspondente ao ID fornecido seja atualizado.
                $stmt = $db->prepare("UPDATE solicitacoes SET status = :status, observacao_admin = :obs WHERE id = :id");
                // Executa a consulta SQL com os parâmetros fornecidos, atualizando o status e a observação da solicitação no banco de dados.
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

// Verifica a URI da requisição para determinar qual método do SolicitacaoController deve ser chamado, permitindo que diferentes ações relacionadas às solicitações sejam tratadas com base na URL acessada.
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$controller = new SolicitacaoController();

// Verifica a URI da requisição para determinar qual método do SolicitacaoController deve ser chamado, permitindo que diferentes ações relacionadas às solicitações sejam tratadas com base na URL acessada.
if (strpos($requestUri, 'solicitacao/alterarStatus') !== false) {
    $controller->alterarStatus();
} else {
    $controller->index();
}
?>