<?php
require_once 'factory/conexao.php';

class Doacao {
    public static function listarTodas() {
        $db = Conexao::getConexao();
        $stmt = $db->query("SELECT * FROM doacoes ORDER BY data_doacao DESC");
        return $stmt->fetchAll();
    }

    public static function cadastrar($dados) {
        $db = Conexao::getConexao();
        $stmt = $db->prepare("INSERT INTO doacoes (doador_nome, tipo_doacao, quantidade_ou_valor, descricao) VALUES (:doador_nome, :tipo_doacao, :quantidade_ou_valor, :descricao)");
        
        return $stmt->execute([
            ':doador_nome' => $dados['doador_nome'],
            ':tipo_doacao' => $dados['tipo_doacao'],
            ':quantidade_ou_valor' => $dados['quantidade_ou_valor'],
            ':descricao' => $dados['descricao']
        ]);
    }
}
?>