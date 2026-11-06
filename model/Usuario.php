<?php
require_once 'factory/conexao.php';

class Usuario {
    public static function autenticar($email, $senha) {
        $db = Conexao::getConexao();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            return $usuario;
        }
        return false;
    }

    public static function criarAdminPadrao() {
        $db = Conexao::getConexao();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = 'admin@ong.com'");
        $stmt->execute();
        $admin = $stmt->fetch();

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
            // Garante que a senha seja 123456 mesmo se já existir
            $update = $db->prepare("UPDATE usuarios SET senha = :senha WHERE email = 'admin@ong.com'");
            $update->execute([':senha' => $senhaHash]);
        }
    }

    public static function criarBeneficiarioPadrao() {
        $db = Conexao::getConexao();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = 'beneficiario@ong.com'");
        $stmt->execute();
        $beneficiario = $stmt->fetch();

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
    }
}
?>