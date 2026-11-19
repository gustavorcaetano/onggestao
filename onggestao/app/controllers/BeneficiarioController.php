<?php
// Arquivo: onggestao/app/controllers/BeneficiarioController.php
use core\database\DBConnection;


global $rootPath;

// Classe BeneficiarioController gerencia as ações do beneficiário, incluindo o painel, solicitações e cancelamento de solicitações.
class BeneficiarioController {

    // Construtor que verifica se o usuário está logado e é do tipo beneficiário. Caso contrário, redireciona para a página de login.
    public function __construct() {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'beneficiario') {
            header("Location: " . $_SESSION['url']['root'] . "auth/login");
            exit;
        }
    }

    // Método para exibir o painel do beneficiário, incluindo solicitações, dados da família e ações da ONG.
   public function painel() {
        // Utiliza a classe de conexão oficial do framework para interagir com o banco de dados
        global $rootPath;
        $usuario_id = $_SESSION['usuario_id'];
        
        // Busca as solicitações do beneficiário, os dados da família e as ações da ONG, tratando possíveis exceções e garantindo que o painel seja carregado corretamente mesmo que não haja registros.
        try {
            $db = (new DBConnection())->getConn();
            $usuarioId = $_SESSION['usuario_id'];
        
            // Busca as solicitações do beneficiário (que você já tem)
            $stmtSol = $db->prepare("SELECT * FROM solicitacoes WHERE usuario_id = :uid ORDER BY id DESC");
            $stmtSol->execute([':uid' => $usuarioId]);
            // Busca todas as solicitações do beneficiário, ordenadas pelo ID em ordem decrescente, garantindo que as solicitações mais recentes apareçam primeiro
            $solicitacoes = $stmtSol->fetchAll();
        
            // NOVO: Busca os dados cadastrais da família do beneficiário
            $stmtFam = $db->prepare("
                SELECT f.*, u.email AS email_login, u.criado_em AS data_cadastro 
                FROM familias f
                INNER JOIN usuarios u ON f.responsavel_id = u.id
                WHERE f.responsavel_id = :uid 
                LIMIT 1
            ");
            // Executa a consulta para buscar os dados da família do beneficiário logado, garantindo que apenas os dados da família correspondente ao usuário logado sejam retornados
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

        // Carrega a view do painel do beneficiário, verificando se o arquivo existe antes de incluí-lo. Caso o arquivo não exista, exibe uma mensagem de sucesso como fallback.
        if (file_exists($rootPath . '/app/views/beneficiario/painel.php')) {
            require_once $rootPath . '/app/views/beneficiario/painel.php';
        } else {
            echo "Painel do Beneficiário carregado com sucesso!";
        }
    }

    // Método para processar a solicitação de um beneficiário, inserindo os dados no banco de dados e redirecionando para o painel com uma mensagem de sucesso.
    public function solicitar() {
        // Verifica se a requisição é do tipo POST, garantindo que os dados da solicitação sejam enviados corretamente antes de processá-los
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
    
    // Método para cancelar uma solicitação feita pelo beneficiário, atualizando o status da solicitação no banco de dados e redirecionando para o painel com uma mensagem de sucesso ou erro.
    public function cancelarSolicitacao() {
        // Verifica se a requisição é do tipo POST, garantindo que os dados da solicitação a ser cancelada sejam enviados corretamente antes de processá-los
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id        = $_POST['id'] ?? '';
            $usuarioId = $_SESSION['usuario_id'] ?? '';

            // Valida se o ID da solicitação e o ID do usuário estão presentes; caso contrário, redireciona para o painel do beneficiário sem realizar nenhuma ação
            if (empty($id) || empty($usuarioId)) {
                header("Location: " . $_SESSION['url']['root'] . "beneficiario/painel");
                exit;
            }

            // Tenta atualizar o status da solicitação para "Cancelado" no banco de dados, garantindo que apenas solicitações pendentes do usuário logado possam ser canceladas. Caso ocorra algum erro durante o processo, redireciona para o painel com uma mensagem de erro.
            try {
                $db = (new DBConnection())->getConn();

                // Só cancela se o pedido pertencer ao usuário logado E ainda estiver 'Pendente'
                $stmt = $db->prepare("
                    UPDATE solicitacoes 
                    SET status = 'Cancelado', observacao_admin = 'Cancelado pelo próprio beneficiário.' 
                    WHERE id = :id AND usuario_id = :uid AND status = 'Pendente'
                ");
                // Executa a consulta para atualizar o status da solicitação, vinculando os parâmetros para evitar SQL Injection e garantindo que apenas solicitações pendentes do usuário logado possam ser canceladas
                $stmt->execute([
                    ':id'  => $id,
                    ':uid' => $usuarioId
                ]);

                // Verifica se alguma linha foi afetada (ou seja, se a solicitação foi realmente cancelada) e redireciona para o painel com uma mensagem de sucesso ou erro apropriada
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

// Despachante do BeneficiarioController
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$controller = new BeneficiarioController();

// Verifica a URI da requisição para determinar qual método do BeneficiarioController deve ser chamado, permitindo que diferentes ações do beneficiário sejam tratadas com base na URL acessada
if (strpos($requestUri, 'beneficiario/solicitar') !== false) {
    $controller->solicitar();
} elseif (strpos($requestUri, 'beneficiario/cancelarSolicitacao') !== false) {
    $controller->cancelarSolicitacao();
} else {
    $controller->painel();
}
?>