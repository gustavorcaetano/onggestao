<?php
class Conexao {
    private static $instance = null;

    public static function getConexao() {
        if (self::$instance === null) {
            $host = 'localhost';
            $dbname = 'administrador_onggestao'; // Nome exato do banco no cPanel
            $username = 'administrador_onggestao'; // Usu���rio gerado no cPanel 
            $password = 'onggestao_123';     // A senha forte que voc��� definiu

            try {
                self::$instance = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Erro na conex���o com o banco de dados: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
?>