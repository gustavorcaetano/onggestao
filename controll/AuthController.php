<?php
require_once 'model/Usuario.php';
require_once 'factory/conexao.php';

class AuthController {

    /**
     * Efetua o login do usuário (Admin, Doador ou Beneficiário)
     */
    public function login() {
        $erro = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $senha = trim($_POST['senha'] ?? '');

            $usuario = Usuario::autenticar($email, $senha);

            if ($usuario) {
                $_SESSION['usuario_id']    = $usuario['id'];
                $_SESSION['usuario_nome']  = $usuario['nome'];
                $_SESSION['usuario_tipo']  = $usuario['tipo'];
                $_SESSION['usuario_email'] = $usuario['email'];

                // Redirecionamento baseado estritamente no tipo de usuário
                if ($usuario['tipo'] === 'admin') {
                    header("Location: /index.php?url=admin/dashboard");
                } elseif ($usuario['tipo'] === 'doador') {
                    header("Location: /index.php?url=doador/painel");
                } else {
                    header("Location: /index.php?url=beneficiario/painel");
                }
                exit;
            } else {
                // Redireciona com alerta visual de erro na URL
                header("Location: /index.php?url=auth/login&erro=" . urlencode("E-mail ou senha incorretos. Tente novamente."));
                exit;
            }
        }

        require_once 'view/auth/login.php';
    }

    /**
     * Cadastra um novo usuário (Doador ou Beneficiário)
     */
    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome  = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $senha = password_hash(trim($_POST['senha'] ?? ''), PASSWORD_DEFAULT);
            $tipo  = $_POST['tipo'] ?? 'beneficiario';

            try {
                $db = Conexao::getConexao();
                
                // Verificar se o e-mail já está em uso
                $stmtCheck = $db->prepare("SELECT id FROM usuarios WHERE email = :email");
                $stmtCheck->execute([':email' => $email]);
                
                if ($stmtCheck->fetch()) {
                    header("Location: /index.php?url=auth/login&erro=" . urlencode("Este e-mail já está cadastrado em nosso sistema."));
                    exit;
                }

                // Inserir novo usuário
                $stmt = $db->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (:nome, :email, :senha, :tipo)");
                $stmt->execute([
                    ':nome'  => $nome,
                    ':email' => $email,
                    ':senha' => $senha,
                    ':tipo'  => $tipo
                ]);

                header("Location: /index.php?url=auth/login&sucesso=" . urlencode("Conta criada com sucesso! Faça login para acessar."));
                exit;

            } catch (PDOException $e) {
                header("Location: /index.php?url=auth/login&erro=" . urlencode("Ocorreu um erro ao criar a conta. Tente novamente."));
                exit;
            }
        }
    }

    /**
     * Encerra a sessão do usuário
     */
    public function logout() {
        session_destroy();
        header("Location: /index.php?url=home");
        exit;
    }
}
?>