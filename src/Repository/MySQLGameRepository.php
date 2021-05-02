<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Repository;

final class MySQLGameRepository
{
    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }
}