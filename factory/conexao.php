<?php
class Conexao {
    private static $instance = null;

    public static function getConexao() {
        if (self::$instance === null) {
            $host = 'localhost';
            $dbname = 'onggestao'; // Nome do seu banco local no MAMP
            $username = 'root';    // Usuário padrão do MAMP
            $password = 'root';    // Senha padrão do MAMP (ou vazia '' se você não usa senha)
            $port = '8889';        // Porta padrão do MySQL no MAMP

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