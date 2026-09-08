<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function connection(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $host     = Env::get('DB_HOST', '127.0.0.1');
        $port     = Env::get('DB_PORT', '3306');
        $database = Env::get('DB_DATABASE', 'smartenroll');
        $username = Env::get('DB_USERNAME', 'root');
        $password = Env::get('DB_PASSWORD', '');

        // Cloud SQL unix socket paths (start with "/") don't use host:port syntax
        if (str_starts_with($host, '/')) {
            $dsn = "mysql:unix_socket={$host};dbname={$database};charset=utf8mb4";
        } else {
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
        }

        try {
            self::$instance = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // real prepared statements -> SQL injection protection
            ]);
        } catch (PDOException $e) {
            // Never leak DSN/credentials in the error output
            error_log('Database connection failed: ' . $e->getMessage());
            throw new PDOException('Database connection failed. Check server logs.', (int) $e->getCode());
        }

        return self::$instance;
    }
}
