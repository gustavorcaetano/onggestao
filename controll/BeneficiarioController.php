<?php
require_once 'model/Solicitacao.php';
require_once 'model/Familia.php';

class BeneficiarioController {
    
    public function __construct() {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'beneficiario') {
            header("Location: /index.php?url=auth/login");
            exit;
        }
    }

    public function painel() {
        $usuario_id = $_SESSION['usuario_id'];
        
        // Busca a família vinculada ao usuário logado ou usa ID padrão para teste
        $db = Conexao::getConexao();
        $stmt = $db->prepare("SELECT id FROM familias WHERE responsavel_id = :responsavel_id LIMIT 1");
        $stmt->execute([':responsavel_id' => $usuario_id]);
        $familia = $stmt->fetch();
        
        $familia_id = $familia ? $familia['id'] : 1; // ID fallback para testes
        
        $solicitacoes = Solicitacao::listarPorFamilia($familia_id);
        require_once 'view/beneficiario/painel.php';
    }

    public function solicitar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario_id = $_SESSION['usuario_id'];
            
            $db = Conexao::getConexao();
            $stmt = $db->prepare("SELECT id FROM familias WHERE responsavel_id = :responsavel_id LIMIT 1");
            $stmt->execute([':responsavel_id' => $usuario_id]);
            $familia = $stmt->fetch();
            $familia_id = $familia ? $familia['id'] : 1;

            $dados = [
                'familia_id' => $familia_id,
                'categoria' => $_POST['categoria'] ?? 'Cesta Básica',
                'descricao' => $_POST['descricao'] ?? '',
                'status' => 'pendente'
            ];

            Solicitacao::cadastrar($dados);
            header("Location: /index.php?url=beneficiario/painel");
            exit;
        }
    }
}
?>