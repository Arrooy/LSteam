<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Repository;

use PDO;

use DateTime;
use SallePW\SlimApp\Model\UserSaveRepository;

final class MySQLUserSaveRepository implements UserSaveRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function addSearch(int $userId, string $searchText): void{
        $query = <<<'QUERY'
        INSERT INTO Search(user_id, search, created_at)
        VALUES(:user_id, :search, :created_at)
        QUERY;
        
        $current_date = new DateTime();

        $statement = $this->database->connection()->prepare($query);

        $createdAt = $current_date->format(self::DATE_FORMAT);

        $statement->bindParam('user_id', $userId, PDO::PARAM_STR);
        $statement->bindParam('search', $searchText, PDO::PARAM_STR);
        $statement->bindParam('created_at', $createdAt, PDO::PARAM_STR);

        $statement->execute();
    }
}