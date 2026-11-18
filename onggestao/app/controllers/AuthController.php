<?php

use core\database\DBConnection;

// Garante que o arquivo do Usuario seja incluído corretamente
global $rootPath;
if (file_exists($rootPath . '/app/models/Usuario.php')) {
    require_once $rootPath . '/app/models/Usuario.php';
}

class AuthController {

    public function login() {
        $erro = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $senha = trim($_POST['senha'] ?? '');

            $usuario = Usuario::autenticar($email, $senha);

            if ($usuario) {
                if (isset($usuario['status']) && $usuario['status'] === 'pendente') {
                    header("Location: " . $_SESSION['url']['root'] . "auth/login&erro=" . urlencode("Sua conta de beneficiário ainda está em análise pela administração."));
                    exit;
                }

                $_SESSION['usuario_id']    = $usuario['id'];
                $_SESSION['usuario_nome']  = $usuario['nome'];
                $_SESSION['usuario_tipo']  = $usuario['tipo'];
                $_SESSION['usuario_email'] = $usuario['email'];

                $rootUrl = $_SESSION['url']['root'];

                if ($usuario['tipo'] === 'admin') {
                    header("Location: " . $rootUrl . "admin/dashboard");
                } elseif ($usuario['tipo'] === 'doador') {
                    header("Location: " . $rootUrl . "doador/painel");
                } else {
                    header("Location: " . $rootUrl . "beneficiario/painel");
                }
                exit;
            } else {
                header("Location: " . $_SESSION['url']['root'] . "auth/login&erro=" . urlencode("E-mail ou senha incorretos. Tente novamente."));
                exit;
            }
        }
        
        global $rootPath;
        require_once $rootPath . '/app/views/auth/login.php';
    }
    
    public function registrar() {
        global $rootPath;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome  = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $senha = password_hash(trim($_POST['senha'] ?? ''), PASSWORD_DEFAULT);
            $tipo  = trim($_POST['tipo'] ?? 'doador');

            $statusUsuario = ($tipo === 'beneficiario') ? 'pendente' : 'ativo';

            try {
                $db = (new DBConnection())->getConn();
                
                // 1. Verifica se e-mail já existe
                $stmtCheck = $db->prepare("SELECT id FROM usuarios WHERE email = :email");
                $stmtCheck->execute([':email' => $email]);

                if ($stmtCheck->fetch()) {
                    header("Location: " . $_SESSION['url']['root'] . "auth/registrar&erro=" . urlencode("Este e-mail já está cadastrado."));
                    exit;
                }

                // 2. Insere na tabela de usuários
                $stmt = $db->prepare("INSERT INTO usuarios (nome, email, senha, tipo, status, criado_em) VALUES (:nome, :email, :senha, :tipo, :status, NOW())");
                $stmt->execute([
                    ':nome'   => $nome,
                    ':email'  => $email,
                    ':senha'  => $senha,
                    ':tipo'   => $tipo,
                    ':status' => $statusUsuario
                ]);
                $usuarioId = $db->lastInsertId();

                // 3. Se for beneficiário, insere na tabela familias respeitando exatamente as colunas da imagem
                if ($tipo === 'beneficiario') {
                    $cpf     = trim($_POST['cpf'] ?? '');
                    $tel     = trim($_POST['telefone'] ?? '');
                    $membros = intval($_POST['quantidade_membros'] ?? 1);
                    // Tratamento infalível da renda familiar
                    $rendaBruta = $_POST['renda_familiar'] ?? '0';
                    
                    // Remove tudo exceto números, vírgula e ponto
                    $rendaLimpa = preg_replace('/[^\d,\.]/', '', $rendaBruta);
                    
                    // Se houver ponto e vírgula (formato brasileiro R$ 1.200,00)
                    if (strpos($rendaLimpa, ',') !== false && strpos($rendaLimpa, '.') !== false) {
                        $rendaLimpa = str_replace('.', '', $rendaLimpa); // Remove o ponto de milhar
                        $rendaLimpa = str_replace(',', '.', $rendaLimpa); // Troca a vírgula por ponto decimal
                    } elseif (strpos($rendaLimpa, ',') !== false) {
                        // Se tiver apenas vírgula (ex: 1200,00)
                        $rendaLimpa = str_replace(',', '.', $rendaLimpa);
                    }
                    
                    $renda = floatval($rendaLimpa);
                    $cep     = trim($_POST['cep'] ?? '');
                    $rua     = trim($_POST['rua'] ?? '');
                    $num     = trim($_POST['numero'] ?? '');
                    $bairro  = trim($_POST['bairro'] ?? '');
                    
                    // Tratando a cidade e estado (ex: "São Paulo - SP" ou separado)
                    $cidadeCompleta = trim($_POST['cidade'] ?? '');
                    $cidade = $cidadeCompleta;
                    $estado = '';
                    if (strpos($cidadeCompleta, '-') !== false) {
                        list($cidade, $estado) = explode('-', $cidadeCompleta);
                        $cidade = trim($cidade);
                        $estado = trim($estado);
                    }

                    $stmtFam = $db->prepare("INSERT INTO familias (responsavel_id, nome_responsavel, email, cpf, telefone, cep, rua, numero, bairro, cidade, estado, quantidade_membros, renda_familiar, status, criado_em) VALUES (:uid, :nome, :email, :cpf, :tel, :cep, :rua, :num, :bairro, :cidade, :estado, :membros, :renda, 'Em Análise', NOW())");
                    
                    $stmtFam->execute([
                        ':uid'     => $usuarioId,
                        ':nome'    => $nome,
                        ':email'   => $email,
                        ':cpf'     => $cpf,
                        ':tel'     => $tel,
                        ':cep'     => $cep,
                        ':rua'     => $rua,
                        ':num'     => $num,
                        ':bairro'  => $bairro,
                        ':cidade'  => $cidade,
                        ':estado'  => $estado,
                        ':membros' => $membros,
                        ':renda'   => $renda
                    ]);

                    header("Location: " . $_SESSION['url']['root'] . "auth/login&sucesso=" . urlencode("Cadastro realizado! Sua conta de beneficiário aguarda análise da administração."));
                } else {
                    header("Location: " . $_SESSION['url']['root'] . "auth/login&sucesso=" . urlencode("Cadastro realizado com sucesso! Faça seu login."));
                }
                exit;
            } catch (Exception $e) {
                header("Location: " . $_SESSION['url']['root'] . "auth/registrar&erro=" . urlencode("Erro ao cadastrar: " . $e->getMessage()));
                exit;
            }
        }
        
        require_once $rootPath . '/app/views/auth/registrar.php';
    }

    public static function esqueciSenha() {
        global $rootPath;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header("Location: " . $_SESSION['url']['root'] . "auth/login&sucesso=Instruções enviadas para o seu e-mail!");
            exit;
        } else {
            require_once $rootPath . '/app/views/auth/esqueci_senha.php';
        }
    }
    
    public function enviarRecuperacao() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');

            try {
                $db = (new DBConnection())->getConn();
                $stmt = $db->prepare("SELECT id, nome FROM usuarios WHERE email = :email LIMIT 1");
                $stmt->execute([':email' => $email]);
                $usuario = $stmt->fetch();

                if ($usuario) {
                    $token = bin2hex(random_bytes(32));
                    $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

                    $stmtUpdate = $db->prepare("UPDATE usuarios SET reset_token = :token, reset_expira = :expira WHERE id = :id");
                    $stmtUpdate->execute([
                        ':token'  => $token,
                        ':expira' => $expira,
                        ':id'     => $usuario['id']
                    ]);

                    $linkRedefinir = $_SESSION['url']['root'] . "auth/novaSenha&token=" . $token;

                    // Redireciona exibindo o link de acesso diretamente para teste ágil
                    header("Location: " . $_SESSION['url']['root'] . "auth/login&sucesso=" . urlencode("Link gerado com sucesso! Clique aqui para redefinir: " . $linkRedefinir));
                    exit;
                }

                header("Location: " . $_SESSION['url']['root'] . "auth/login&sucesso=" . urlencode("Se o e-mail estiver cadastrado, as instruções foram geradas."));
                exit;
            } catch (Exception $e) {
                header("Location: " . $_SESSION['url']['root'] . "auth/login&erro=" . urlencode("Erro ao processar recuperação."));
                exit;
            }
        }
    }

    public function novaSenha() {
        global $rootPath;
        $token = $_GET['token'] ?? '';

        // Exibe a view de alteração de senha passando o token
        if (file_exists($rootPath . '/app/views/auth/nova_senha.php')) {
            require_once $rootPath . '/app/views/auth/nova_senha.php';
        } else {
            echo "Erro: View de nova senha não encontrada.";
        }
    }

    public function salvarNovaSenha() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'] ?? '';
            $novaSenha = $_POST['senha'] ?? '';

            if (empty($token) || empty($novaSenha)) {
                header("Location: " . $_SESSION['url']['root'] . "auth/login&erro=" . urlencode("Dados inválidos."));
                exit;
            }

            try {
                $db = (new DBConnection())->getConn();
                $stmt = $db->prepare("SELECT id FROM usuarios WHERE reset_token = :token AND reset_expira >= NOW() LIMIT 1");
                $stmt->execute([':token' => $token]);
                $usuario = $stmt->fetch();

                if (!$usuario) {
                    header("Location: " . $_SESSION['url']['root'] . "auth/login&erro=" . urlencode("Token inválido ou expirado."));
                    exit;
                }

                $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

                $stmtUpdate = $db->prepare("UPDATE usuarios SET senha = :senha, reset_token = NULL, reset_expira = NULL WHERE id = :id");
                $stmtUpdate->execute([
                    ':senha' => $senhaHash,
                    ':id'    => $usuario['id']
                ]);

                header("Location: " . $_SESSION['url']['root'] . "auth/login&sucesso=" . urlencode("Senha alterada com sucesso! Faça login."));
                exit;
            } catch (Exception $e) {
                header("Location: " . $_SESSION['url']['root'] . "auth/login&erro=" . urlencode("Erro ao redefinir senha."));
                exit;
            }
        }
    }

    public function logout() {
        session_destroy();
        header("Location: " . $_SESSION['url']['root'] . "home");
        exit;
    }
}

// Despachante do AuthController
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$controller = new AuthController();

if (strpos($requestUri, 'auth/registrar') !== false) {
    $controller->registrar();
} elseif (strpos($requestUri, 'auth/enviarRecuperacao') !== false) {
    $controller->enviarRecuperacao();
} elseif (strpos($requestUri, 'auth/novaSenha') !== false) {
    $controller->novaSenha();
} elseif (strpos($requestUri, 'auth/salvarNovaSenha') !== false) {
    $controller->salvarNovaSenha();
} elseif (strpos($requestUri, 'auth/esqueciSenha') !== false) {
    AuthController::esqueciSenha();
} elseif (strpos($requestUri, 'auth/logout') !== false) {
    $controller->logout();
} else {
    $controller->login();
}
?>