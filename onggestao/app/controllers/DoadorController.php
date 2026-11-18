<?php

use core\database\DBConnection;

class DoadorController {

    public function __construct() {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'doador') {
            header("Location: " . $_SESSION['url']['root'] . "auth/login");
            exit;
        }
    }

    public function painel() {
        global $rootPath;
        $db = (new DBConnection())->getConn();

        $nome  = $_SESSION['usuario_nome'] ?? '';
        $email = $_SESSION['usuario_email'] ?? '';

        // Busca o histórico de doações vinculadas ao nome ou e-mail do doador
        $stmt = $db->prepare("SELECT * FROM doacoes WHERE doador_nome = :nome OR doador_nome = :email ORDER BY id DESC");
        $stmt->execute([
            ':nome'  => $nome,
            ':email' => $email
        ]);
        $doacoes = $stmt->fetchAll();

        require_once $rootPath . '/app/views/doador/painel.php';
    }

    public function doar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo = trim($_POST['tipo_doacao'] ?? 'Alimento / Cesta Básica');
            $qtdValor = trim($_POST['quantidade_ou_valor'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $doadorNome = $_SESSION['usuario_nome'] ?? 'Doador';

            if (empty($qtdValor)) {
                header("Location: " . $_SESSION['url']['root'] . "doador/painel&erro=" . urlencode("Informe a quantidade ou o valor da doação."));
                exit;
            }

            try {
                $db = (new DBConnection())->getConn();
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

if (strpos($requestUri, 'doador/doar') !== false) {
    $controller->doar();
} else {
    $controller->painel();
}
?>