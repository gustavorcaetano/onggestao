<?php

use core\database\DBConnection;

class Usuario {

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

    public static function criarAdminPadrao() {
        try {
            $db = (new DBConnection())->getConn();
            $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = 'admin@ong.com'");
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            $senhaHash = password_hash("123456", PASSWORD_DEFAULT);

            if (!$admin) {
                $insert = $db->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (:nome, :email, :senha, :tipo)");
                $insert->execute([
                    ':nome' => 'Administrador',
                    ':email' => 'admin@ong.com',
                    ':senha' => $senhaHash,
                    ':tipo' => 'admin'
                ]);
            } else {
                $update = $db->prepare("UPDATE usuarios SET senha = :senha WHERE email = 'admin@ong.com'");
                $update->execute([':senha' => $senhaHash]);
            }
        } catch (PDOException $e) {
            // Tratamento silencioso ou log de erro se necessário
        }
    }

    public static function criarBeneficiarioPadrao() {
        try {
            $db = (new DBConnection())->getConn();
            $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = 'beneficiario@ong.com'");
            $stmt->execute();
            $beneficiario = $stmt->fetch(PDO::FETCH_ASSOC);

            $senhaHash = password_hash("123456", PASSWORD_DEFAULT);

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
        } catch (PDOException $e) {
            // Tratamento silencioso
        }
    }
}
?>