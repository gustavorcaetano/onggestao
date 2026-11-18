<?php
// Arquivo: onggestao/app/controllers/AuthController.php
use core\database\DBConnection;

// Garante que o arquivo do Usuario seja incluído corretamente
global $rootPath;
if (file_exists($rootPath . '/app/models/Usuario.php')) {
    require_once $rootPath . '/app/models/Usuario.php';
}

// Garante que o arquivo do DBConnection seja incluído corretamente
class AuthController {

// Método para exibir a página de login e processar o login do usuário
    public function login() {
        $erro = "";

        // Verifica se o formulário de login foi enviado via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $senha = trim($_POST['senha'] ?? '');

            // Autentica o usuário utilizando o método da classe Usuario
            $usuario = Usuario::autenticar($email, $senha);

            // Se o usuário for autenticado com sucesso, inicia a sessão e redireciona para a página apropriada com base no tipo de usuário
            if ($usuario) {
                if (isset($usuario['status']) && $usuario['status'] === 'pendente') {
                    header("Location: " . $_SESSION['url']['root'] . "auth/login&erro=" . urlencode("Sua conta de beneficiário ainda está em análise pela administração."));
                    exit;
                }

                // Armazena informações do usuário na sessão para uso posterior
                $_SESSION['usuario_id']    = $usuario['id'];
                $_SESSION['usuario_nome']  = $usuario['nome'];
                $_SESSION['usuario_tipo']  = $usuario['tipo'];
                $_SESSION['usuario_email'] = $usuario['email'];

                // Redireciona o usuário para a página apropriada com base no tipo de usuário
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
    
    // Método para exibir a página de registro e processar o registro do usuário
    public function registrar() {
        global $rootPath;

        // Verifica se o formulário de registro foi enviado via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome  = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            // Garante que a senha seja tratada corretamente, mesmo que o campo não esteja definido
            $senha = password_hash(trim($_POST['senha'] ?? ''), PASSWORD_DEFAULT);
            $tipo  = trim($_POST['tipo'] ?? 'doador');
            // Define o status do usuário com base no tipo: beneficiários começam como 'pendente', enquanto outros tipos são 'ativo'
            $statusUsuario = ($tipo === 'beneficiario') ? 'pendente' : 'ativo';

            // Tenta cadastrar o usuário e, se for um beneficiário, também cadastra na tabela de famílias
            try {
                $db = (new DBConnection())->getConn();
                
                // 1. Verifica se e-mail já existe
                $stmtCheck = $db->prepare("SELECT id FROM usuarios WHERE email = :email");
                $stmtCheck->execute([':email' => $email]);

                // Se o e-mail já estiver cadastrado, redireciona de volta para a página de registro com uma mensagem de erro
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
                // Obtém o ID do usuário recém-criado para uso posterior, especialmente se for um beneficiário que precisa ser registrado na tabela de famílias
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
                    
                    // Converte para float, garantindo que seja um número válido
                    $renda = floatval($rendaLimpa);
                    // Tratamento dos campos de endereço, garantindo que valores vazios sejam tratados como strings vazias
                    $cep     = trim($_POST['cep'] ?? '');
                    $rua     = trim($_POST['rua'] ?? '');
                    $num     = trim($_POST['numero'] ?? '');
                    $bairro  = trim($_POST['bairro'] ?? '');
                    
                    // Tratando a cidade e estado (ex: "São Paulo - SP" ou separado)
                    $cidadeCompleta = trim($_POST['cidade'] ?? '');
                    $cidade = $cidadeCompleta;
                    $estado = '';
                    // Separa cidade e estado se estiverem no formato "Cidade - UF"
                    if (strpos($cidadeCompleta, '-') !== false) {
                        list($cidade, $estado) = explode('-', $cidadeCompleta);
                        $cidade = trim($cidade);
                        $estado = trim($estado);
                    }

                    // 4. Insere na tabela familias com os dados do beneficiário, garantindo que todos os campos sejam tratados corretamente
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
            }
            // Caso ocorra algum erro durante o processo de cadastro, captura a exceção e redireciona para a página de registro com uma mensagem de erro detalhada
            catch (Exception $e) {
                header("Location: " . $_SESSION['url']['root'] . "auth/registrar&erro=" . urlencode("Erro ao cadastrar: " . $e->getMessage()));
                exit;
            }
        }
        
        // Inclui a view de registro, permitindo que o usuário preencha o formulário de cadastro
        require_once $rootPath . '/app/views/auth/registrar.php';
    }

    // Método para exibir a página de recuperação de senha
    public static function esqueciSenha() {
        global $rootPath;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header("Location: " . $_SESSION['url']['root'] . "auth/login&sucesso=Instruções enviadas para o seu e-mail!");
            exit;
        } else {
            require_once $rootPath . '/app/views/auth/esqueci_senha.php';
        }
    }
    
    // Método para enviar o e-mail de recuperação de senha
    public function enviarRecuperacao() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');

            // Verifica se o e-mail foi fornecido
            try {
                $db = (new DBConnection())->getConn();
                $stmt = $db->prepare("SELECT id, nome FROM usuarios WHERE email = :email LIMIT 1");
                $stmt->execute([':email' => $email]);
                $usuario = $stmt->fetch(); // Busca o usuário pelo e-mail fornecido

                // Se o usuário existir, gera um token de recuperação e define uma expiração para o token (1 hora a partir do momento da solicitação)
                if ($usuario) {
                    $token = bin2hex(random_bytes(32));
                    $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

                    $stmtUpdate = $db->prepare("UPDATE usuarios SET reset_token = :token, reset_expira = :expira WHERE id = :id");
                    $stmtUpdate->execute([
                        ':token'  => $token,
                        ':expira' => $expira,
                        ':id'     => $usuario['id']
                    ]);

                    // Gera o link de redefinição de senha, incluindo o token como parâmetro na URL
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

    // Método para exibir a página de alteração de senha, recebendo o token como parâmetro na URL
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

    // Método para salvar a nova senha do usuário após a validação do token
    public function salvarNovaSenha() {
        // Verifica se a requisição foi feita via POST, garantindo que os dados foram enviados de um formulário
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'] ?? ''; // Garante que o token seja obtido do formulário enviado via POST
            $novaSenha = $_POST['senha'] ?? ''; // Garante que a nova senha seja obtida do formulário enviado via POST

            // Valida se o token e a nova senha foram fornecidos; caso contrário, redireciona para a página de login com uma mensagem de erro
            if (empty($token) || empty($novaSenha)) {
                header("Location: " . $_SESSION['url']['root'] . "auth/login&erro=" . urlencode("Dados inválidos."));
                exit;
            }

            // Tenta atualizar a senha do usuário no banco de dados, validando o token e garantindo que ele não tenha expirado
            try {
                $db = (new DBConnection())->getConn();
                $stmt = $db->prepare("SELECT id FROM usuarios WHERE reset_token = :token AND reset_expira >= NOW() LIMIT 1");
                $stmt->execute([':token' => $token]);
                // Busca o usuário correspondente ao token fornecido, garantindo que o token ainda seja válido (não expirado)
                $usuario = $stmt->fetch();

                // Se o usuário não for encontrado (token inválido ou expirado), redireciona para a página de login com uma mensagem de erro
                if (!$usuario) {
                    header("Location: " . $_SESSION['url']['root'] . "auth/login&erro=" . urlencode("Token inválido ou expirado."));
                    exit;
                }

                // Gera o hash da nova senha utilizando o algoritmo de hash padrão do PHP, garantindo que a senha seja armazenada de forma segura no banco de dados
                $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

                // Atualiza a senha do usuário no banco de dados, removendo o token de redefinição e a data de expiração para garantir que o token não possa ser reutilizado
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

    // Método para encerrar a sessão do usuário e redirecionar para a página de login
    public function logout() {
        session_destroy();
        header("Location: " . $_SESSION['url']['root'] . "home");
        exit;
    }
}

// Despachante do AuthController
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
// Instancia o controlador de autenticação para lidar com as requisições relacionadas a login, registro, recuperação de senha e logout
$controller = new AuthController();

// Verifica a URI da requisição para determinar qual método do AuthController deve ser chamado, permitindo que diferentes ações de autenticação sejam tratadas com base na URL acessada
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