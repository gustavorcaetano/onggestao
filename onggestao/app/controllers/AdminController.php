<?php
// Arquivo: onggestao/app/models/Doacao.php
use core\database\DBConnection;

// Classe para gerenciar operações com a tabela de doações
global $rootPath;
// Inclui os modelos necessários para o AdminController
if (file_exists($rootPath . '/app/models/Familia.php')) {
    // Inclui o modelo Familia.php apenas se o arquivo existir, evitando erros de inclusão
    require_once $rootPath . '/app/models/Familia.php';
}
if (file_exists($rootPath . '/app/models/Solicitacao.php')) {
    require_once $rootPath . '/app/models/Solicitacao.php';
}
if (file_exists($rootPath . '/app/models/Doacao.php')) {
    require_once $rootPath . '/app/models/Doacao.php';
}

// Classe para gerenciar as ações administrativas
class AdminController {
    // Construtor que verifica se o usuário é um administrador antes de permitir o acesso às funções do controlador
    public function __construct() {
        // Verifica se o usuário está logado e se é do tipo 'admin'; caso contrário, redireciona para a página de login
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
            header("Location: " . $_SESSION['url']['root'] . "auth/login");
            exit;
        }
    }

    // Função para exibir o dashboard do administrador, mostrando informações sobre famílias, beneficiários pendentes e pedidos pendentes
    public function dashboard() {
        // Declaração da variável global $rootPath para uso dentro do método
        global $rootPath;
        // Tenta executar as consultas ao banco de dados para obter informações necessárias para o dashboard
        try {
            $db = (new DBConnection())->getConn();
            
            // Traz todas as famílias, exceto as que estão pendentes de análise pública
            $familias = $db->query("SELECT * FROM familias WHERE status != 'Em Análise' AND status != 'pendente' ORDER BY id DESC")->fetchAll();
            
            // Busca os pendentes para o sino na dashboard
            $beneficiariosPendentes = $db->query("SELECT * FROM usuarios WHERE tipo = 'beneficiario' AND (status = 'pendente' OR status = 'Em Análise') ORDER BY id DESC")->fetchAll();
            $totalPendentes = count($beneficiariosPendentes);

            // Contagem para a bolinha de notificação na aba de Pedidos
            $pedidosPendentesCount = $db->query("SELECT COUNT(*) FROM solicitacoes WHERE status = 'Pendente'")->fetchColumn();

        }
        // Caso ocorra algum erro durante a execução das consultas, captura a exceção e inicializa as variáveis com valores padrão
        catch (Exception $e) {
            $familias = [];
            $beneficiariosPendentes = [];
            $totalPendentes = 0;
            $pedidosPendentesCount = 0;
        }
        // Inclui a view do dashboard do administrador, passando as variáveis obtidas das consultas para exibição
        require_once $rootPath . '/app/views/admin/dashboard.php';
    }

    // Método para criar o usuário administrador padrão
    public function cadastrarAdmin() {
        // Verifica se o formulário foi enviado via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome  = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $senha = password_hash(trim($_POST['senha'] ?? ''), PASSWORD_DEFAULT);

            try {
                $db = (new DBConnection())->getConn();
                // Verifica se o e-mail já está cadastrado antes de inserir um novo administrador
                $stmtCheck = $db->prepare("SELECT id FROM usuarios WHERE email = :email");
                // Executa a consulta para verificar se o e-mail já existe no banco de dados
                $stmtCheck->execute([':email' => $email]);

                // Se o e-mail já estiver cadastrado, redireciona para o dashboard com uma mensagem de erro
                if ($stmtCheck->fetch()) {
                    header("Location: " . $_SESSION['url']['root'] . "admin/dashboard&erro=" . urlencode("Este e-mail já está cadastrado."));
                    exit;
                }

                // Se o e-mail não estiver cadastrado, insere um novo administrador no banco de dados
                $stmt = $db->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (:nome, :email, :senha, 'admin')");
                $stmt->execute([':nome' => $nome, ':email' => $email, ':senha' => $senha]);

                // Redireciona para o dashboard com uma mensagem de sucesso após o cadastro do administrador
                header("Location: " . $_SESSION['url']['root'] . "admin/dashboard&sucesso=" . urlencode("Administrador cadastrado com sucesso!"));
                exit;
            }
            // Caso ocorra algum erro durante a execução da consulta, captura a exceção e redireciona para o dashboard com uma mensagem de erro
            catch (PDOException $e) {
                header("Location: " . $_SESSION['url']['root'] . "admin/dashboard&erro=" . urlencode("Erro ao cadastrar administrador."));
                exit;
            }
        }
    }

    // Método para criar o usuário beneficiário padrão
    public function cadastrarFamilia() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Limpa a string da renda monetária (ex: "R$ 1.200,00" vira 1200.00)
                $rendaBruta = $_POST['renda_familiar'] ?? '0';
                // Remove todos os caracteres que não são dígitos da string da renda
                $rendaLimpa = preg_replace('/[^\d]/', '', $rendaBruta);
                $rendaFloat = floatval($rendaLimpa) / 100;

                $db = (new DBConnection())->getConn();
                $stmt = $db->prepare("INSERT INTO familias (nome_responsavel, email, cpf, telefone, quantidade_membros, renda_familiar, cep, rua, numero, bairro, cidade, status, criado_em) VALUES (:nome, :email, :cpf, :tel, :membros, :renda, :cep, :rua, :num, :bairro, :cidade, 'Ativo', NOW())");
                // Executa a consulta com os dados fornecidos, utilizando valores padrão para o usuário caso não estejam definidos na sessão
                $stmt->execute([
                    ':nome'     => trim($_POST['nome_responsavel'] ?? ''),
                    ':email'    => trim($_POST['email'] ?? ''),
                    ':cpf'      => trim($_POST['cpf'] ?? ''),
                    ':tel'      => trim($_POST['telefone'] ?? ''),
                    ':membros'  => intval($_POST['quantidade_membros'] ?? 0),
                    ':renda'    => $rendaFloat,
                    ':cep'      => trim($_POST['cep'] ?? ''),
                    ':rua'      => trim($_POST['rua'] ?? ''),
                    ':num'      => trim($_POST['numero'] ?? ''),
                    ':bairro'   => trim($_POST['bairro'] ?? ''),
                    ':cidade'   => trim($_POST['cidade'] ?? '')
                ]);

                header("Location: " . $_SESSION['url']['root'] . "admin/dashboard&sucesso=" . urlencode("Família cadastrada com sucesso!"));
                exit;
            } catch (Exception $e) {
                header("Location: " . $_SESSION['url']['root'] . "admin/dashboard&erro=" . urlencode("Erro ao cadastrar família."));
                exit;
            }
        }
    }

    public function atualizarFamilia() {
        // Verifica se a requisição foi feita via POST, garantindo que os dados foram enviados de um formulário
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id       = $_POST['id'] ?? '';
            $nome     = trim($_POST['nome_responsavel'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $cpf      = trim($_POST['cpf'] ?? '');
            $telefone = trim($_POST['telefone'] ?? '');
            $membros  = intval($_POST['quantidade_membros'] ?? 1);
            
            // Limpa a string da renda monetária
            $rendaBruta = $_POST['renda_familiar'] ?? '0';
            $rendaLimpa = preg_replace('/[^\d]/', '', $rendaBruta);
            $rendaFloat = floatval($rendaLimpa) / 100;

            $cep      = trim($_POST['cep'] ?? '');
            $rua      = trim($_POST['rua'] ?? '');
            $numero   = trim($_POST['numero'] ?? '');
            $bairro   = trim($_POST['bairro'] ?? '');
            $cidade   = trim($_POST['cidade'] ?? '');

            try {
                $db = (new DBConnection())->getConn();

                // Busca os dados atuais da família antes de alterar (para sincronizar o usuário, se existir)
                $stmtAtual = $db->prepare("SELECT responsavel_id, email FROM familias WHERE id = :id");
                $stmtAtual->execute([':id' => $id]);
                $famAtual = $stmtAtual->fetch();
                
                // Atualiza a tabela familias incluindo email e cpf
                $stmt = $db->prepare("UPDATE familias SET nome_responsavel = :nome, email = :email, cpf = :cpf, telefone = :tel, quantidade_membros = :membros, renda_familiar = :renda, cep = :cep, rua = :rua, numero = :num, bairro = :bairro, cidade = :cidade WHERE id = :id");
                // Executa a consulta com os dados fornecidos, vinculando os parâmetros para evitar SQL Injection
                $stmt->execute([
                    ':nome'    => $nome,
                    ':email'   => $email,
                    ':cpf'     => $cpf,
                    ':tel'     => $telefone,
                    ':membros' => $membros,
                    ':renda'   => $rendaFloat,
                    ':cep'     => $cep,
                    ':rua'     => $rua,
                    ':num'     => $numero,
                    ':bairro'  => $bairro,
                    ':cidade'  => $cidade,
                    ':id'      => $id
                ]);

                // Sincroniza nome e e-mail na tabela usuarios caso o beneficiário possua login
                if ($famAtual && !empty($email)) {
                    if (!empty($famAtual['responsavel_id'])) {
                        $stmtUser = $db->prepare("UPDATE usuarios SET nome = :nome, email = :email WHERE id = :uid");
                        $stmtUser->execute([':nome' => $nome, ':email' => $email, ':uid' => $famAtual['responsavel_id']]);
                    } elseif (!empty($famAtual['email'])) {
                        $stmtUser = $db->prepare("UPDATE usuarios SET nome = :nome, email = :email WHERE email = :emailAntigo");
                        $stmtUser->execute([':nome' => $nome, ':email' => $email, ':emailAntigo' => $famAtual['email']]);
                    }
                }

                header("Location: " . $_SESSION['url']['root'] . "admin/dashboard&sucesso=" . urlencode("Família atualizada com sucesso!"));
                exit;
            } catch (Exception $e) {
                header("Location: " . $_SESSION['url']['root'] . "admin/dashboard&erro=" . urlencode("Erro ao atualizar família: " . $e->getMessage()));
                exit;
            }
        }
    }

    public function acoes() {
        global $rootPath;
        try {
            // Utiliza a classe de conexão oficial do framework
            $db = (new DBConnection())->getConn();
            $acoes = $db->query("SELECT * FROM acoes ORDER BY id DESC")->fetchAll();
            
            // Busca os beneficiários pendentes para exibir no dashboard do administrador
            $beneficiariosPendentes = $db->query("SELECT * FROM usuarios WHERE tipo = 'beneficiario' AND (status = 'pendente' OR status = 'Em Análise') ORDER BY id DESC")->fetchAll();
            $totalPendentes = count($beneficiariosPendentes);
            $pedidosPendentesCount = $db->query("SELECT COUNT(*) FROM solicitacoes WHERE status = 'Pendente'")->fetchColumn();
        } 
        // Caso ocorra algum erro durante a execução das consultas, captura a exceção e inicializa as variáveis com valores padrão
        catch (Exception $e) {
            $acoes = [];
            $beneficiariosPendentes = [];
            $totalPendentes = 0;
            $pedidosPendentesCount = 0;
        }
        // Inclui a view de ações do administrador, passando as variáveis obtidas das consultas para exibição
        require_once $rootPath . '/app/views/admin/acoes.php';
    }

    public function cadastrarAcao() {
        // Verifica se a requisição foi feita via POST, garantindo que os dados foram enviados de um formulário
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo     = trim($_POST['titulo'] ?? '');
            $descricao  = trim($_POST['descricao'] ?? '');
            $imagemPath = '';

            try {
                // Verifica se uma imagem foi enviada e se não houve erro no upload
                if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                    $pastaUpload = 'public/assets/uploads/';
                    if (!is_dir($pastaUpload)) {
                        mkdir($pastaUpload, 0755, true);
                    }
                    
                    // Gera um nome único para a imagem usando uniqid() e mantém a extensão original do arquivo
                    $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
                    $nomeArquivo = uniqid('acao_') . '.' . $extensao;
                    $destino = $pastaUpload . $nomeArquivo;

                    // Move o arquivo enviado para o diretório de uploads e atualiza o caminho da imagem no banco de dados
                    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                        $imagemPath = '/' . $destino;
                    }
                }

                // Insere a nova ação no banco de dados, incluindo o título, descrição e caminho da imagem (se houver)
                $db = (new DBConnection())->getConn();
                $stmt = $db->prepare("INSERT INTO acoes (titulo, descricao, imagem, criado_em) VALUES (:titulo, :descricao, :imagem, NOW())");
                $stmt->execute([':titulo' => $titulo, ':descricao' => $descricao, ':imagem' => $imagemPath]);

                header("Location: " . $_SESSION['url']['root'] . "admin/acoes&sucesso=" . urlencode("Nova ação cadastrada!"));
                exit;
            } catch (Exception $e) {
                header("Location: " . $_SESSION['url']['root'] . "admin/acoes&erro=" . urlencode("Erro ao publicar ação."));
                exit;
            }
        }
    }

    public function atualizarAcao() {
        // Verifica se a requisição foi feita via POST, garantindo que os dados foram enviados de um formulário
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id        = $_POST['id'] ?? '';
            $titulo    = trim($_POST['titulo'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');

            try {
                $db = (new DBConnection())->getConn();

                // Verifica se uma nova imagem foi enviada; caso contrário, mantém a imagem atual
                if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                    $pastaUpload = 'public/assets/uploads/';
                    if (!is_dir($pastaUpload)) {
                        mkdir($pastaUpload, 0755, true);
                    }
                    
                    // Gera um nome único para a imagem usando uniqid() e mantém a extensão original do arquivo
                    $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
                    $nomeArquivo = uniqid('acao_') . '.' . $extensao;
                    $destino = $pastaUpload . $nomeArquivo;

                    // Move o arquivo enviado para o diretório de uploads e atualiza o caminho da imagem no banco de dados
                    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                        $imagemPath = '/' . $destino;
                        $stmt = $db->prepare("UPDATE acoes SET titulo = :titulo, descricao = :descricao, imagem = :imagem WHERE id = :id");
                        $stmt->execute([':titulo' => $titulo, ':descricao' => $descricao, ':imagem' => $imagemPath, ':id' => $id]);
                    }
                } 
                // Caso nenhuma nova imagem seja enviada, apenas atualiza o título e a descrição da ação existente
                else {
                    $stmt = $db->prepare("UPDATE acoes SET titulo = :titulo, descricao = :descricao WHERE id = :id");
                    $stmt->execute([':titulo' => $titulo, ':descricao' => $descricao, ':id' => $id]);
                }

                header("Location: " . $_SESSION['url']['root'] . "admin/acoes&sucesso=" . urlencode("Ação atualizada com sucesso!"));
                exit;
            } catch (Exception $e) {
                header("Location: " . $_SESSION['url']['root'] . "admin/acoes&erro=" . urlencode("Erro ao atualizar a ação."));
                exit;
            }
        }
    }

    public function excluirAcao() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $db = (new DBConnection())->getConn();
                // Exclui a ação com base no ID fornecido via POST, garantindo que apenas a ação correta seja removida do banco de dados
                $stmt = $db->prepare("DELETE FROM acoes WHERE id = :id");
                $stmt->execute([':id' => $_POST['id']]);

                header("Location: " . $_SESSION['url']['root'] . "admin/acoes&sucesso=" . urlencode("Ação excluída com sucesso!"));
                exit;
            } catch (Exception $e) {
                header("Location: " . $_SESSION['url']['root'] . "admin/acoes&erro=" . urlencode("Erro ao excluir a ação."));
                exit;
            }
        }
    }
    
    public function aprovarBeneficiario() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtém o ID do beneficiário a ser aprovado a partir dos dados enviados via POST
            $id = $_POST['id'] ?? '';
            try {
                $db = (new DBConnection())->getConn();
                
                // Atualiza o status do usuário para 'ativo' na tabela de usuários
                $stmt = $db->prepare("UPDATE usuarios SET status = 'ativo' WHERE id = :id");
                $stmt->execute([':id' => $id]);

                // Atualiza o status da família associada ao beneficiário para 'Ativo' na tabela de famílias
                $stmtFam = $db->prepare("UPDATE familias SET status = 'Ativo' WHERE responsavel_id = :id");
                $stmtFam->execute([':id' => $id]);

                header("Location: " . $_SESSION['url']['root'] . "admin/dashboard&sucesso=" . urlencode("Beneficiário aprovado! Agora consta como Ativo."));
                exit;
            } catch (Exception $e) {
                header("Location: " . $_SESSION['url']['root'] . "admin/dashboard&erro=" . urlencode("Erro ao aprovar beneficiário."));
                exit;
            }
        }
    }

    // Método para rejeitar um beneficiário, removendo tanto o registro do usuário quanto da família associada
    public function rejeitarBeneficiario() {
        // Verifica se a requisição foi feita via POST, garantindo que os dados foram enviados de um formulário
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            try {
                $db = (new DBConnection())->getConn();

                // Busca o ID do responsável e o e-mail da família antes de excluir, para garantir que os registros corretos sejam removidos
                $stmtFam = $db->prepare("DELETE FROM familias WHERE responsavel_id = :id");
                $stmtFam->execute([':id' => $id]);

                // Exclui o usuário associado ao beneficiário rejeitado, garantindo que não haja registros órfãos no banco de dados
                $stmtUser = $db->prepare("DELETE FROM usuarios WHERE id = :id");
                $stmtUser->execute([':id' => $id]);

                header("Location: " . $_SESSION['url']['root'] . "admin/dashboard&sucesso=" . urlencode("Cadastro rejeitado e removido do sistema."));
                exit;
            } catch (Exception $e) {
                header("Location: " . $_SESSION['url']['root'] . "admin/dashboard&erro=" . urlencode("Erro ao rejeitar cadastro."));
                exit;
            }
        }
    }
    
    public function excluirFamilia() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            try {
                $db = (new DBConnection())->getConn();

                // Verifica se a família existe e obtém o ID do responsável e o e-mail antes de excluir
                $stmtCheck = $db->prepare("SELECT responsavel_id, email FROM familias WHERE id = :id");
                // Executa a consulta para verificar se a família existe e obter os dados necessários
                $stmtCheck->execute([':id' => $id]);
                // Busca os dados da família antes de excluir para verificar se há um usuário associado
                $fam = $stmtCheck->fetch();

                if ($fam) {
                    // Exclui a família e, se houver um usuário associado, também exclui o usuário correspondente
                    $stmtFam = $db->prepare("DELETE FROM familias WHERE id = :id");
                    $stmtFam->execute([':id' => $id]);

                    // Verifica se há um usuário associado à família e o exclui
                    if (!empty($fam['responsavel_id'])) {
                        $stmtUser = $db->prepare("DELETE FROM usuarios WHERE id = :uid");
                        $stmtUser->execute([':uid' => $fam['responsavel_id']]);
                    } 
                    // Se não houver um usuário associado, mas houver um e-mail registrado, exclui o usuário com base no e-mail
                    else {
                        $stmtUser = $db->prepare("DELETE FROM usuarios WHERE email = :email");
                        $stmtUser->execute([':email' => $fam['email']]);
                    }
                }

                header("Location: " . $_SESSION['url']['root'] . "admin/dashboard&sucesso=" . urlencode("Família e conta associada excluídas com sucesso!"));
                exit;
            } catch (Exception $e) {
                header("Location: " . $_SESSION['url']['root'] . "admin/dashboard&erro=" . urlencode("Erro ao excluir família."));
                exit;
            }
        }
    }
}

// Roteamento simples baseado na URI da requisição para determinar qual método do AdminController deve ser chamado
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
// Instancia o controlador AdminController para lidar com as requisições
$controllerInstance = new AdminController();

// 
if (strpos($requestUri, 'admin/cadastrarAcao') !== false) {
    $controllerInstance->cadastrarAcao();
} elseif (strpos($requestUri, 'admin/atualizarAcao') !== false) {
    $controllerInstance->atualizarAcao();
} elseif (strpos($requestUri, 'admin/excluirAcao') !== false) {
    $controllerInstance->excluirAcao();
} elseif (strpos($requestUri, 'admin/acoes') !== false) {
    $controllerInstance->acoes();
} elseif (strpos($requestUri, 'familia/cadastrar') !== false) {
    $controllerInstance->cadastrarFamilia();
} elseif (strpos($requestUri, 'familia/atualizar') !== false) {
    $controllerInstance->atualizarFamilia();
} elseif (strpos($requestUri, 'familia/excluir') !== false) {
    $controllerInstance->excluirFamilia();
} elseif (strpos($requestUri, 'admin/cadastrarAdmin') !== false) {
    $controllerInstance->cadastrarAdmin();
} elseif (strpos($requestUri, 'admin/aprovarBeneficiario') !== false) {
    $controllerInstance->aprovarBeneficiario();
} elseif (strpos($requestUri, 'admin/rejeitarBeneficiario') !== false) {
    $controllerInstance->rejeitarBeneficiario();
} else {
    $controllerInstance->dashboard();
}
?>