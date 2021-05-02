<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Repository;

use PDO;
use SallePW\SlimApp\Model\GameRepository;

final class MySQLGameRepository implements GameRepository
{
    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function addBoughtGame(int $gameId, int $userId): bool
    {
        error_log(print_r($userId, TRUE));
        $query = <<<'QUERY'
        INSERT INTO ownedGames(gameId, userId)
        VALUES(:gid, :uid)
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('gid', $gameId, PDO::PARAM_STR);
        $statement->bindParam('uid', $userId, PDO::PARAM_STR);

        $statement->execute();
        return true;
    }
}