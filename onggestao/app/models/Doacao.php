<?php

use core\database\DBConnection;

class Doacao {
    public static function listarTodas() {
        try {
            $db = (new DBConnection())->getConn();
            $stmt = $db->query("SELECT * FROM doacoes ORDER BY data_doacao DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public static function cadastrar($dados) {
        try {
            $db = (new DBConnection())->getConn();
            $stmt = $db->prepare("INSERT INTO doacoes (doador_nome, tipo_doacao, quantidade_ou_valor, descricao) VALUES (:doador_nome, :tipo_doacao, :quantidade_ou_valor, :descricao)");
            
            return $stmt->execute([
                ':doador_nome'         => $dados['doador_nome'],
                ':tipo_doacao'         => $dados['tipo_doacao'],
                ':quantidade_ou_valor' => $dados['quantidade_ou_valor'],
                ':descricao'           => $dados['descricao']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>