<?php

use core\database\DBConnection;

class Usuario {

    // Método para autenticar o usuário
    public static function autenticar($email, $senha) {
        try {
            // Utiliza a classe de conexão oficial do framework
            $db = (new DBConnection())->getConn();
            
            $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                return $usuario;
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Método para criar o usuário administrador padrão
    public static function criarAdminPadrao() {
        try {
            $db = (new DBConnection())->getConn();
            $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = 'admin@ong.com'");
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            $senhaHash = password_hash("123456", PASSWORD_DEFAULT);

            // Se o administrador não existir, cria um novo; caso contrário, atualiza a senha
            if (!$admin) {
                $insert = $db->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (:nome, :email, :senha, :tipo)");
                $insert->execute([
                    ':nome' => 'Administrador',
                    ':email' => 'admin@ong.com',
                    ':senha' => $senhaHash,
                    ':tipo' => 'admin'
                ]);
            } 
            // Se o administrador já existir, atualiza a senha
            else {
                $update = $db->prepare("UPDATE usuarios SET senha = :senha WHERE email = 'admin@ong.com'");
                $update->execute([':senha' => $senhaHash]);
            }
        } catch (PDOException $e) {
            // Tratamento silencioso ou log de erro se necessário
        }
    }

    // Método para criar o usuário beneficiário padrão
    public static function criarBeneficiarioPadrao() {
        try {
            // Utiliza a classe de conexão oficial do framework
            $db = (new DBConnection())->getConn();
            // Verifica se o beneficiário já existe
            $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = 'beneficiario@ong.com'");
            $stmt->execute();
            // Busca o beneficiário no banco de dados
            $beneficiario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Gera o hash da senha padrão
            $senhaHash = password_hash("123456", PASSWORD_DEFAULT);

            // Se o beneficiário não existir, cria um novo; caso contrário, atualiza a senha
            if (!$beneficiario) {
                $insert = $db->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (:nome, :email, :senha, :tipo)");
                $insert->execute([
                    ':nome' => 'Eduardo Pereira',
                    ':email' => 'beneficiario@ong.com',
                    ':senha' => $senhaHash,
                    ':tipo' => 'beneficiario'
                ]);
            } else {
                $update = $db->prepare("UPDATE usuarios SET senha = :senha WHERE email = 'beneficiario@ong.com'");
                $update->execute([':senha' => $senhaHash]);
            }
        } 
        // Se ocorrer algum erro de banco de dados, o catch irá capturar a exceção e o tratamento será silencioso
        catch (PDOException $e) {
            // Tratamento silencioso
        }
    }
}
?>