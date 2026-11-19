<?php
// Arquivo: onggestao/app/controllers/HomeController.php
require_once 'factory/conexao.php';

// Classe HomeController gerencia a exibição da página inicial do sistema, incluindo a listagem das ações cadastradas no banco de dados.
class HomeController {
    // Método para exibir a página inicial, buscando as ações cadastradas no banco de dados e carregando a view correspondente.
    public function index() {
        $db = Conexao::getConexao();
        // Busca as ações cadastradas no banco
        $acoes = $db->query("SELECT * FROM acoes ORDER BY id DESC LIMIT 6")->fetchAll();
        
        // Carrega a view da página inicial, verificando se o arquivo existe antes de incluí-lo. Caso o arquivo não exista, exibe uma mensagem de sucesso como fallback.
        require_once 'view/home.php';
    }
}
?>