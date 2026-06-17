<?php
defined('ORION') || exit('Acesso negado.');

class Database
{
    private static $instance = null;

    public static function connection()
    {
        if (self::$instance instanceof PDO) {
            return self::$instance;
        }

        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            if (APP_ENV === 'local') {
                exit('Erro de conexão com o banco: ' . $e->getMessage());
            }
            http_response_code(500);
            exit('Serviço temporariamente indisponível.');
        }

        return self::$instance;
    }
}
