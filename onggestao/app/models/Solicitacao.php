<?php
// Arquivo: onggestao/app/models/Solicitacao.php
use core\database\DBConnection;

class Solicitacao {
    
    // Listar pedidos de um beneficiário específico
    public static function listarPorUsuario($usuario_id) {
        // Utiliza a classe de conexão oficial do framework
        try {
            $db = (new DBConnection())->getConn();
            $stmt = $db->prepare("SELECT * FROM solicitacoes WHERE usuario_id = :usuario_id ORDER BY criado_em DESC");
            // Bind do parâmetro para evitar SQL Injection
            $stmt->execute([':usuario_id' => $usuario_id]);
            // Retorna todas as solicitações do usuário como um array associativo
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Listar TODas as solicitações para o Admin
    public static function listarTodas() {
        try {
            // Utiliza a classe de conexão oficial do framework
            $db = (new DBConnection())->getConn();
            // Consulta SQL para buscar todas as solicitações, incluindo o nome do usuário que fez a solicitação
            $sql = "SELECT s.*, u.nome as nome_usuario 
                    FROM solicitacoes s 
                    LEFT JOIN usuarios u ON s.usuario_id = u.id 
                    ORDER BY s.criado_em DESC";
            // Executa a consulta e retorna os resultados como um array associativo
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Cadastrar nova solicitação feita pelo beneficiário
    public static function cadastrar($dados) {
        try {
            $db = (new DBConnection())->getConn();
            // Prepara a consulta SQL para inserir uma nova solicitação no banco de dados
            $sql = "INSERT INTO solicitacoes (usuario_id, solicitante_nome, categoria, descricao, status) 
                    VALUES (:usuario_id, :solicitante_nome, :categoria, :descricao, :status)";
            $stmt = $db->prepare($sql);
            // Executa a consulta com os dados fornecidos, utilizando valores padrão para o usuário e solicitante caso não estejam definidos na sessão
            return $stmt->execute([
                ':usuario_id'       => $_SESSION['usuario_id'] ?? 1,
                ':solicitante_nome' => $_SESSION['usuario_nome'] ?? 'Beneficiário',
                ':categoria'        => $dados['categoria'],
                ':descricao'        => $dados['descricao'],
                ':status'           => 'Pendente'
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>