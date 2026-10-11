<?php
require_once 'model/Familia.php';
require_once 'model/Solicitacao.php';
require_once 'model/Doacao.php';
require_once 'factory/conexao.php';

class AdminController {

    public function __construct() {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
            header("Location: /index.php?url=auth/login");
            exit;
        }
    }

    public function dashboard() {
        // Busca todas as famílias cadastradas para exibir na tabela e permitir busca
        $familias = Familia::listarTodas();
        require_once 'view/admin/dashboard.php';
    }

    public function cadastrarAdmin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome  = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $senha = password_hash(trim($_POST['senha'] ?? ''), PASSWORD_DEFAULT);

            try {
                $db = Conexao::getConexao();

                // Verifica se o e-mail já existe
                $stmtCheck = $db->prepare("SELECT id FROM usuarios WHERE email = :email");
                $stmtCheck->execute([':email' => $email]);

                if ($stmtCheck->fetch()) {
                    header("Location: /index.php?url=admin/dashboard&erro=" . urlencode("Este e-mail já está cadastrado para outro usuário."));
                    exit;
                }

                $stmt = $db->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (:nome, :email, :senha, 'admin')");
                $stmt->execute([
                    ':nome'  => $nome,
                    ':email' => $email,
                    ':senha' => $senha
                ]);

                header("Location: /index.php?url=admin/dashboard&sucesso=" . urlencode("Novo administrador cadastrado com sucesso!"));
                exit;

            } catch (PDOException $e) {
                header("Location: /index.php?url=admin/dashboard&erro=" . urlencode("Erro ao cadastrar administrador. Tente novamente."));
                exit;
            }
        }
    }

    public function acoes() {
        $db = Conexao::getConexao();
        $acoes = $db->query("SELECT * FROM acoes ORDER BY id DESC")->fetchAll();
        require_once 'view/admin/acoes.php';
    }

    public function cadastrarAcao() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo     = trim($_POST['titulo'] ?? '');
            $descricao  = trim($_POST['descricao'] ?? '');
            $imagemPath = '';

            try {
                // Processar o upload da imagem se houver arquivo enviado
                if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                    $pastaUpload = 'assets/uploads/';
                    if (!is_dir($pastaUpload)) {
                        mkdir($pastaUpload, 0755, true);
                    }
                    
                    $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
                    $nomeArquivo = uniqid('acao_') . '.' . $extensao;
                    $destino = $pastaUpload . $nomeArquivo;

                    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                        $imagemPath = '/' . $destino; // Caminho relativo salvo no banco
                    }
                }

                $db = Conexao::getConexao();
                $stmt = $db->prepare("INSERT INTO acoes (titulo, descricao, imagem, criado_em) VALUES (:titulo, :descricao, :imagem, NOW())");
                $stmt->execute([
                    ':titulo'    => $titulo,
                    ':descricao' => $descricao,
                    ':imagem'    => $imagemPath
                ]);

                header("Location: /index.php?url=admin/acoes&sucesso=" . urlencode("Nova ação cadastrada e publicada no feed!"));
                exit;

            } catch (Exception $e) {
                header("Location: /index.php?url=admin/acoes&erro=" . urlencode("Erro ao publicar a ação. Tente novamente."));
                exit;
            }
        }
    }
}
?>