<?php
class Database
{
    private static $pdo;

    public static function connect()
    {
        if (!self::$pdo) {
            try {
                $host = 'localhost';
                $db = 'lojagamer';
                $user = 'root';
                $pass = '';
                self::$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erro de conexão: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}