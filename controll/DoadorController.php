<?php
require_once 'model/Doacao.php';

class DoadorController {

    public function __construct() {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'doador') {
            header("Location: /index.php?url=auth/login");
            exit;
        }
    }

    public function painel() {
        $usuario_id = $_SESSION['usuario_id'];
        $db = Conexao::getConexao();

        // Buscar histórico de doações deste doador específico (por e-mail/id)
        $email = $_SESSION['usuario_email'] ?? '';
        $stmt = $db->prepare("SELECT * FROM doacoes WHERE doador_nome = :nome OR doador_nome = :email ORDER BY id DESC");
        $stmt->execute([
            ':nome'  => $_SESSION['usuario_nome'],
            ':email' => $email
        ]);
        $doacoes = $stmt->fetchAll();

        require_once 'view/doador/painel.php';
    }

    public function doar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = [
                'doador_nome'         => $_SESSION['usuario_nome'],
                'tipo_doacao'         => $_POST['tipo_doacao'] ?? 'Alimento',
                'quantidade_ou_valor' => $_POST['quantidade_ou_valor'] ?? '',
                'descricao'           => $_POST['descricao'] ?? ''
            ];

            $db = Conexao::getConexao();
            $stmt = $db->prepare("INSERT INTO doacoes (doador_nome, tipo_doacao, quantidade_ou_valor, descricao, data_doacao) VALUES (:doador_nome, :tipo_doacao, :quantidade_ou_valor, :descricao, NOW())");
            $stmt->execute($dados);

            header("Location: /index.php?url=doador/painel&sucesso=" . urlencode("Sua doação foi registrada com sucesso! A ONG agradece seu apoio."));
        exit;
        }
    }
}
?>