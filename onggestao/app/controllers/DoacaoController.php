<?php
// Arquivo: onggestao/app/controllers/DoacaoController.php
use core\database\DBConnection;

// Verifica se a classe Doacao existe antes de incluí-la, garantindo que o arquivo de modelo seja carregado apenas se estiver presente no caminho especificado
global $rootPath;
if (file_exists($rootPath . '/app/models/Doacao.php')) {
    require_once $rootPath . '/app/models/Doacao.php';
}

// Classe DoacaoController gerencia as ações relacionadas às doações, incluindo listagem e cadastro de novas doações.
class DoacaoController {
    
    // Construtor que verifica se o usuário está logado e é do tipo admin. Caso contrário, redireciona para a página de login.
    public function __construct() {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
            header("Location: " . $_SESSION['url']['root'] . "auth/login");
            exit;
        }
    }

    // Método para exibir a lista de doações cadastradas, incluindo informações sobre beneficiários pendentes e pedidos pendentes.
    public function index() {
        global $rootPath;

        // Verifica se a classe Doacao existe antes de chamar o método listarTodas, garantindo que a listagem de doações seja realizada apenas se o modelo estiver presente
        $doacoes = class_exists('Doacao') ? Doacao::listarTodas() : [];
        
        // Tenta buscar informações sobre beneficiários pendentes e pedidos pendentes no banco de dados, tratando possíveis exceções e garantindo que a página seja carregada corretamente mesmo que não haja registros.
        try {
            $db = (new DBConnection())->getConn();
            $beneficiariosPendentes = $db->query("SELECT * FROM usuarios WHERE tipo = 'beneficiario' AND (status = 'pendente' OR status = 'Em An��lise') ORDER BY id DESC")->fetchAll();
            $totalPendentes = count($beneficiariosPendentes);
            $pedidosPendentesCount = $db->query("SELECT COUNT(*) FROM solicitacoes WHERE status = 'Pendente'")->fetchColumn();
        } catch (Exception $e) {
            $beneficiariosPendentes = [];
            $totalPendentes = 0;
            $pedidosPendentesCount = 0;
        }
        try {
            $db = (new DBConnection())->getConn();
            $beneficiariosPendentes = $db->query("SELECT * FROM usuarios WHERE tipo = 'beneficiario' AND (status = 'pendente' OR status = 'Em An��lise') ORDER BY id DESC")->fetchAll();
            $totalPendentes = count($beneficiariosPendentes);
        } catch (Exception $e) {
            $beneficiariosPendentes = [];
            $totalPendentes = 0;
        }
        
        // Carrega a view de doações, verificando se o arquivo existe antes de incluí-lo. Caso o arquivo não exista, exibe a view do dashboard como fallback.
        $viewPath = $rootPath . '/app/views/admin/doacoes.php';
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            require_once $rootPath . '/app/views/admin/dashboard.php';
        }
    }

    // Método para cadastrar uma nova doação, processando os dados enviados via POST e redirecionando para a lista de doações após o cadastro.
    public function cadastrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = [
                'doador_nome'         => $_POST['doador_nome'] ?? 'An�0�0nimo',
                'tipo_doacao'         => $_POST['tipo_doacao'] ?? 'Alimento',
                'quantidade_ou_valor' => $_POST['quantidade_ou_valor'] ?? '',
                'descricao'           => $_POST['descricao'] ?? ''
            ];

            //  Verifica se a classe Doacao existe antes de chamar o método cadastrar, garantindo que o cadastro de doações seja realizado apenas se o modelo estiver presente
            if (class_exists('Doacao')) {
                Doacao::cadastrar($dados);
            }
            
            header("Location: " . $_SESSION['url']['root'] . "doacao/index");
            exit;
        }
    }
}

// Despachante do DoacaoController, que verifica a URI da requisição para determinar qual método do DoacaoController deve ser chamado, permitindo que diferentes ações relacionadas às doações sejam tratadas com base na URL acessada.
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$controllerInstance = new DoacaoController();

// Verifica a URI da requisição para determinar qual método do DoacaoController deve ser chamado, permitindo que diferentes ações relacionadas às doações sejam tratadas com base na URL acessada.
if (strpos($requestUri, 'doacao/cadastrar') !== false) {
    $controllerInstance->cadastrar();
} else {
    $controllerInstance->index();
}
?>