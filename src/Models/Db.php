<?php

namespace Models;

use PDO;

class Db
{
    protected function getConnection(): PDO
    {
        $dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=UTF8";
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

        return new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $options);
    }
}
