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
        $sql = "INSERT INTO familias (responsavel_id, nome_responsavel, email, cpf, telefone, quantidade_membros, renda_familiar, cep, rua, numero, bairro, cidade, estado, status) 
                VALUES (:responsavel_id, :nome_responsavel, :email, :cpf, :telefone, :quantidade_membros, :renda_familiar, :cep, :rua, :numero, :bairro, :cidade, :estado, :status)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':responsavel_id'     => $_SESSION['usuario_id'] ?? 1,
            ':nome_responsavel'   => $dados['nome_responsavel'],
            ':email'              => $dados['email'] ?? null,
            ':cpf'                => $dados['cpf'],
            ':telefone'           => $dados['telefone'],
            ':quantidade_membros' => $dados['quantidade_membros'],
            ':renda_familiar'     => $dados['renda_familiar'],
            ':cep'                => $dados['cep'] ?? null,
            ':rua'                => $dados['rua'] ?? null,
            ':numero'             => $dados['numero'] ?? null,
            ':bairro'             => $dados['bairro'] ?? null,
            ':cidade'             => $dados['cidade'] ?? null,
            ':estado'             => $dados['estado'] ?? null,
            ':status'             => 'analise'
        ]);
    }

    public static function atualizar($id, $dados) {
        $db = Conexao::getConexao();
        $sql = "UPDATE familias SET nome_responsavel = :nome_responsavel, email = :email, cpf = :cpf, telefone = :telefone, quantidade_membros = :quantidade_membros, renda_familiar = :renda_familiar, cep = :cep, rua = :rua, numero = :numero, bairro = :bairro, cidade = :cidade, estado = :estado WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':id'                 => $id,
            ':nome_responsavel'   => $dados['nome_responsavel'],
            ':email'              => $dados['email'] ?? null,
            ':cpf'                => $dados['cpf'],
            ':telefone'           => $dados['telefone'],
            ':quantidade_membros' => $dados['quantidade_membros'],
            ':renda_familiar'     => $dados['renda_familiar'],
            ':cep'                => $dados['cep'] ?? null,
            ':rua'                => $dados['rua'] ?? null,
            ':numero'             => $dados['numero'] ?? null,
            ':bairro'             => $dados['bairro'] ?? null,
            ':cidade'             => $dados['cidade'] ?? null,
            ':estado'             => $dados['estado'] ?? null
        ]);
    }

    public static function excluir($id, $justificativa) {
        $db = Conexao::getConexao();
        $stmt = $db->prepare("DELETE FROM familias WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>
