<?php
require_once 'factory/conexao.php';

class Solicitacao {
    
    // Listar pedidos de um beneficiário específico
    public static function listarPorUsuario($usuario_id) {
        $db = Conexao::getConexao();
        $stmt = $db->prepare("SELECT * FROM solicitacoes WHERE usuario_id = :usuario_id ORDER BY criado_em DESC");
        $stmt->execute([':usuario_id' => $usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Listar TODas as solicitações para o Admin
    public static function listarTodas() {
        $db = Conexao::getConexao();
        $sql = "SELECT s.*, u.nome as nome_usuario 
                FROM solicitacoes s 
                LEFT JOIN usuarios u ON s.usuario_id = u.id 
                ORDER BY s.criado_em DESC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cadastrar nova solicitação feita pelo beneficiário
    public static function cadastrar($dados) {
        $db = Conexao::getConexao();
        $sql = "INSERT INTO solicitacoes (usuario_id, solicitante_nome, categoria, descricao, status) 
                VALUES (:usuario_id, :solicitante_nome, :categoria, :descricao, :status)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':usuario_id'       => $_SESSION['usuario_id'] ?? 1,
            ':solicitante_nome' => $_SESSION['usuario_nome'] ?? 'Beneficiário',
            ':categoria'        => $dados['categoria'],
            ':descricao'        => $dados['descricao'],
            ':status'           => 'Pendente'
        ]);
    }
}
?>