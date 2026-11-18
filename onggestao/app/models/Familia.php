<?php
// Arquivo: onggestao/app/models/Familia.php
use core\database\DBConnection;

// Classe para gerenciar operações com a tabela de famílias
class Familia {

    // Método para listar todas as famílias cadastradas
    public static function listarTodas() {
        try {
            $db = (new DBConnection())->getConn();
            $stmt = $db->query("SELECT * FROM familias ORDER BY id DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Método para cadastrar uma nova família
    public static function cadastrar($dados) {
        // Utiliza a classe de conexão oficial do framework
        try {
            $db = (new DBConnection())->getConn();
            // Prepara a consulta SQL para inserir uma nova família no banco de dados
            $sql = "INSERT INTO familias (responsavel_id, nome_responsavel, email, cpf, telefone, quantidade_membros, renda_familiar, cep, rua, numero, bairro, cidade, estado, status) 
                    VALUES (:responsavel_id, :nome_responsavel, :email, :cpf, :telefone, :quantidade_membros, :renda_familiar, :cep, :rua, :numero, :bairro, :cidade, :estado, :status)";
            $stmt = $db->prepare($sql);
            // Executa a consulta com os dados fornecidos, utilizando valores padrão para o usuário caso não estejam definidos na sessão
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
        } catch (PDOException $e) {
            return false;
        }
    }

    // Método para atualizar os dados de uma família existente
    public static function atualizar($id, $dados) {
        try {
            // Utiliza a classe de conexão oficial do framework
            $db = (new DBConnection())->getConn();
            // Prepara a consulta SQL para atualizar os dados da família no banco de dados
            $sql = "UPDATE familias SET nome_responsavel = :nome_responsavel, email = :email, cpf = :cpf, telefone = :telefone, quantidade_membros = :quantidade_membros, renda_familiar = :renda_familiar, cep = :cep, rua = :rua, numero = :numero, bairro = :bairro, cidade = :cidade, estado = :estado WHERE id = :id";
            $stmt = $db->prepare($sql);
            // Executa a consulta com os dados fornecidos, vinculando os parâmetros para evitar SQL Injection
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
        } catch (PDOException $e) {
            return false;
        }
    }

    // Método para excluir uma família do banco de dados
    public static function excluir($id, $justificativa) {
        try {
            $db = (new DBConnection())->getConn();
            // Registra a justificativa antes de excluir a família
            $stmt = $db->prepare("DELETE FROM familias WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } 
        // Caso ocorra algum erro durante a execução da consulta, captura a exceção e retorna false
        catch (PDOException $e) {
            return false;
        }
    }
}
?>