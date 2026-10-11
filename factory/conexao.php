<?php
class Conexao {
    private static $instance = null;

    public static function getConexao() {
        if (self::$instance === null) {
            $host = 'localhost';
            $dbname = 'onggestao'; // Atualizado para o nome correto do banco
            $username = 'root';    
            $password = 'root';    
            $port = '8889';        

            try {
                self::$instance = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Erro na conexão com o banco de dados: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
?>