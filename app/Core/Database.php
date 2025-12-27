<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                $host = $_ENV['DB_HOST'] ?? 'localhost';
                $dbname = $_ENV['DB_NAME'] ?? '';
                $user = $_ENV['DB_USER'] ?? '';
                $pass = $_ENV['DB_PASS'] ?? '';

                if ($dbname === '' || $user === '') {
                    throw new \Exception('Configuração de banco incompleta no .env');
                }

                $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

                self::$instance = new PDO(
                    $dsn,
                    $user,
                    $pass,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                die('Erro na conexão: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
