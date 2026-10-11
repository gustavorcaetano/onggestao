<?php
require_once 'factory/conexao.php';

class Familia {

    public static function listarTodas() {
        $db = Conexao::getConexao();
        $stmt = $db->query("SELECT * FROM familias ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public static function cadastrar($dados) {
        $db = Conexao::getConexao();
        $sql = "INSERT INTO familias (responsavel_id, nome_responsavel, email, cpf, telefone, quantidade_membros, renda_familiar, endereco, status) 
                VALUES (:responsavel_id, :nome_responsavel, :email, :cpf, :telefone, :quantidade_membros, :renda_familiar, :endereco, :status)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':responsavel_id'     => $_SESSION['usuario_id'] ?? 1,
            ':nome_responsavel'   => $dados['nome_responsavel'],
            ':email'              => $dados['email'] ?? null,
            ':cpf'                => $dados['cpf'],
            ':telefone'           => $dados['telefone'],
            ':quantidade_membros' => $dados['quantidade_membros'],
            ':renda_familiar'     => $dados['renda_familiar'],
            ':endereco'           => $dados['endereco'],
            ':status'             => 'analise'
        ]);
    }

    public static function atualizar($id, $dados) {
        $db = Conexao::getConexao();
        $sql = "UPDATE familias SET nome_responsavel = :nome_responsavel, email = :email, cpf = :cpf, telefone = :telefone, quantidade_membros = :quantidade_membros, renda_familiar = :renda_familiar, endereco = :endereco WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':id'                 => $id,
            ':nome_responsavel'   => $dados['nome_responsavel'],
            ':email'              => $dados['email'] ?? null,
            ':cpf'                => $dados['cpf'],
            ':telefone'           => $dados['telefone'],
            ':quantidade_membros' => $dados['quantidade_membros'],
            ':renda_familiar'     => $dados['renda_familiar'],
            ':endereco'           => $dados['endereco']
        ]);
    }

    public static function excluir($id, $justificativa) {
        $db = Conexao::getConexao();
        // Você pode salvar a justificativa em uma tabela de log ou apenas deletar
        $stmt = $db->prepare("DELETE FROM familias WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>