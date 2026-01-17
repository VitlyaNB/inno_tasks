<?php

namespace App\Database;

use App\Config\Config;
use PDO;
use PDOException;

class Connection
{
    private static ?PDO $pdo = null;

    public static function get(): PDO
    {
        if (self::$pdo === null) {
            $cfg = Config::db();
            $dsn = "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['name']};charset={$cfg['charset']}";
            for ($i = 0; $i < 5; $i++) {
                try {
                    self::$pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);
                } catch (PDOException $e) {
                    error_log("DB connection error: " . $e->getMessage());
                    sleep(2);
                }
            }
        }
        return self::$pdo;
    }
}
