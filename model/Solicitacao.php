<?php
require_once 'factory/conexao.php';

class Solicitacao {
    // Listar solicitações de uma família específica (Visão do Beneficiário)
    public static function listarPorFamilia($familia_id) {
        $db = Conexao::getConexao();
        $stmt = $db->prepare("SELECT * FROM solicitacoes WHERE familia_id = :familia_id ORDER BY criado_em DESC");
        $stmt->execute([':familia_id' => $familia_id]);
        return $stmt->fetchAll();
    }

    // Listar TODAS as solicitações (Visão do Admin)
    public static function listarTodas() {
        $db = Conexao::getConexao();
        $sql = "SELECT s.*, f.nome_responsavel 
                FROM solicitacoes s 
                LEFT JOIN familias f ON s.familia_id = f.id 
                ORDER BY s.criado_em DESC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }

    // Atualizar status pelo Admin
    public static function atualizarStatus($id, $status) {
        $db = Conexao::getConexao();
        $stmt = $db->prepare("UPDATE solicitacoes SET status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public static function cadastrar($dados) {
        $db = Conexao::getConexao();
        $stmt = $db->prepare("INSERT INTO solicitacoes (familia_id, categoria, descricao, status) VALUES (:familia_id, :categoria, :descricao, :status)");
        
        return $stmt->execute([
            ':familia_id' => $dados['familia_id'],
            ':categoria' => $dados['categoria'],
            ':descricao' => $dados['descricao'],
            ':status' => $dados['status'] ?? 'pendente'
        ]);
    }
}
?>