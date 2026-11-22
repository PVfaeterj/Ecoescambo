<?php
require_once __DIR__ . '/../config.php';

final class Conexao
{
    private static ?PDO $instancia = null;

    public static function obter(): PDO
    {
        if (self::$instancia === null) {
            self::$instancia = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NOME . ';charset=utf8mb4',
                DB_USUARIO,
                DB_SENHA,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        }

        return self::$instancia;
    }
}

function banco(): PDO
{
    return Conexao::obter();
}
