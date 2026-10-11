<?php
require_once 'factory/conexao.php';

class HomeController {
    public function index() {
        $db = Conexao::getConexao();
        // Busca as ações cadastradas no banco
        $acoes = $db->query("SELECT * FROM acoes ORDER BY id DESC LIMIT 6")->fetchAll();
        
        require_once 'view/home.php';
    }
}
?>