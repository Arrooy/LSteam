<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Repository;

use Exception;
use PDO;
use SallePW\SlimApp\Model\Game;
use SallePW\SlimApp\Model\GameRepository;

final class MySQLGameRepository implements GameRepository
{
    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function addBoughtGame(Game $game, int $userId): bool
    {
        try {
            $query = <<<'QUERY'
            INSERT INTO games(gameId, titol, price, thumbnail, metacriticStore, releaseDate)
            VALUES(:gameId, :titol, :price, :thumbnail, :metacriticStore, :releaseDate)
            QUERY;

            $statement = $this->database->connection()->prepare($query);

            $statement->bindParam('gameId', $game->getGameId(), PDO::PARAM_STR);
            $statement->bindParam('titol', $game->getTitol(), PDO::PARAM_STR);
            $statement->bindParam('price', $game->getPrice(), PDO::PARAM_STR);
            $statement->bindParam('thumbnail', $game->getThumbnail(), PDO::PARAM_STR);
            $statement->bindParam('metacriticStore', $game->getMetacritireleaseDatecStore(), PDO::PARAM_STR);
            $statement->bindParam('releaseDate', $game->getReleaseDate(), PDO::PARAM_STR);
            $statement->bindParam('owned', $game->getOwned(), PDO::PARAM_STR);

            $statement->execute();

            try{
                $query = <<<'QUERY'
                INSERT INTO ownedGames(gameId, userId)
                VALUES(:gameId, :userId)
                QUERY;

                $statement = $this->database->connection()->prepare($query);

                $statement->bindParam('gameId', $game->getGameId(), PDO::PARAM_STR);
                $statement->bindParam('userId', $userId(), PDO::PARAM_STR);
                
                $statement->execute();

            } catch (Exception $e) {
                return false
            }
            return true;

        } catch (Exception $e) {
            return false;
        }
    }

    public function getBoughtGames(int $userId): array{
        try {

            $query = <<< 'QUERY'
            SELECT gameId FROM ownedGames WHERE userId=:uid
            QUERY;

            $statement = $this->database->connection()->prepare($query);
            $statement->bindParam('uid', $userId, PDO::PARAM_STR);

            $statement->execute();
            $res = $statement->fetchAll();

            if (!is_array($res)) return [];

            $ids = [];
            foreach ($res as $id){
                array_push($ids, $id['gameId']);
            }
            return $ids;

        } catch (Exception $e) {
            error_log("EXception!");
            error_log(print_r($e->getMessage(),true));

            return [];
        }
    }
}