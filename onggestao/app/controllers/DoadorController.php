<?php
// Arquivo: onggestao/app/controllers/DoadorController.php
use core\database\DBConnection;

// Verifica se a classe Doador existe antes de incluí-la, garantindo que o arquivo de modelo seja carregado apenas se estiver presente no caminho especificado
class DoadorController {

    // Construtor que verifica se o usuário está logado e é do tipo doador. Caso contrário, redireciona para a página de login.
    public function __construct() {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'doador') {
            header("Location: " . $_SESSION['url']['root'] . "auth/login");
            exit;
        }
    }

    // Método para exibir o painel do doador, incluindo histórico de doações e formulário para novas doações.
    public function painel() {
        global $rootPath;
        $db = (new DBConnection())->getConn();

        // Busca o nome e e-mail do doador logado a partir da sessão, garantindo que apenas as informações do usuário autenticado sejam utilizadas para buscar o histórico de doações.
        $nome  = $_SESSION['usuario_nome'] ?? '';
        $email = $_SESSION['usuario_email'] ?? '';

        // Busca o histórico de doações vinculadas ao nome ou e-mail do doador
        $stmt = $db->prepare("SELECT * FROM doacoes WHERE doador_nome = :nome OR doador_nome = :email ORDER BY id DESC");
        $stmt->execute([
            ':nome'  => $nome,
            ':email' => $email
        ]);
        // Armazena o resultado da consulta em um array associativo, que será utilizado na view para exibir o histórico de doações do doador logado.
        $doacoes = $stmt->fetchAll();
        // Carrega a view do painel do doador, verificando se o arquivo existe antes de incluí-lo. Caso o arquivo não exista, exibe uma mensagem de sucesso como fallback.
        require_once $rootPath . '/app/views/doador/painel.php';
    }

    // Método para processar a doação feita pelo doador, validando os dados enviados via POST e registrando a doação no banco de dados.
    public function doar() {
        // Verifica se a requisição é do tipo POST, garantindo que apenas envios de formulário sejam processados para registrar uma nova doação.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo = trim($_POST['tipo_doacao'] ?? 'Alimento / Cesta Básica');
            $qtdValor = trim($_POST['quantidade_ou_valor'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $doadorNome = $_SESSION['usuario_nome'] ?? 'Doador';

            // Valida se a quantidade ou valor da doação foi informado, redirecionando de volta ao painel do doador com uma mensagem de erro caso não tenha sido preenchido.
            if (empty($qtdValor)) {
                header("Location: " . $_SESSION['url']['root'] . "doador/painel&erro=" . urlencode("Informe a quantidade ou o valor da doação."));
                exit;
            }

            // Tenta registrar a doação no banco de dados, capturando possíveis exceções e redirecionando para o painel do doador com uma mensagem de sucesso ou erro.
            try {
                $db = (new DBConnection())->getConn();
                // Prepara a consulta SQL para inserir uma nova doação no banco de dados, incluindo o nome do doador, tipo de doação, quantidade ou valor, descrição e a data da doação.
                $stmt = $db->prepare("INSERT INTO doacoes (doador_nome, tipo_doacao, quantidade_ou_valor, descricao, data_doacao) VALUES (:doador_nome, :tipo_doacao, :quantidade_ou_valor, :descricao, NOW())");
                $stmt->execute([
                    ':doador_nome'         => $doadorNome,
                    ':tipo_doacao'         => $tipo,
                    ':quantidade_ou_valor' => $qtdValor,
                    ':descricao'           => $descricao
                ]);

                header("Location: " . $_SESSION['url']['root'] . "doador/painel&sucesso=" . urlencode("Sua doação foi registrada com sucesso! A ONG agradece seu apoio."));
                exit;
            } catch (Exception $e) {
                header("Location: " . $_SESSION['url']['root'] . "doador/painel&erro=" . urlencode("Erro ao registrar doação: " . $e->getMessage()));
                exit;
            }
        }
    }
}

// Despachante do DoadorController
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$controller = new DoadorController();

// Verifica a URI da requisição para determinar qual método do DoadorController deve ser chamado, permitindo que diferentes ações relacionadas às doações sejam tratadas com base na URL acessada.
if (strpos($requestUri, 'doador/doar') !== false) {
    $controller->doar();
} else {
    $controller->painel();
}
?>