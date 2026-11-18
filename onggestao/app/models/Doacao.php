<?php
// Arquivo: onggestao/app/models/Doacao.php
use core\database\DBConnection;

// Classe para gerenciar operações com a tabela de doações
class Doacao {
    public static function listarTodas() {
        try {
            // Utiliza a classe de conexão oficial do framework
            $db = (new DBConnection())->getConn();
            // Prepara a consulta SQL para buscar todas as doações, ordenadas pela data da doação em ordem decrescente
            $stmt = $db->query("SELECT * FROM doacoes ORDER BY data_doacao DESC");
            // Retorna todas as doações como um array associativo
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Método para cadastrar uma nova doação
    public static function cadastrar($dados) {
        try {
            $db = (new DBConnection())->getConn();
            // Prepara a consulta SQL para inserir uma nova doação no banco de dados
            $stmt = $db->prepare("INSERT INTO doacoes (doador_nome, tipo_doacao, quantidade_ou_valor, descricao) VALUES (:doador_nome, :tipo_doacao, :quantidade_ou_valor, :descricao)");
            
            // Executa a consulta com os dados fornecidos, vinculando os parâmetros para evitar SQL Injection
            return $stmt->execute([
                ':doador_nome'         => $dados['doador_nome'],
                ':tipo_doacao'         => $dados['tipo_doacao'],
                ':quantidade_ou_valor' => $dados['quantidade_ou_valor'],
                ':descricao'           => $dados['descricao']
            ]);
        } 
        // Caso ocorra algum erro durante a execução da consulta, captura a exceção e retorna false
        catch (PDOException $e) {
            return false;
        }
        catch (PDOException $e) {
            return false;
        }
    }
}
?>