<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Repository;

use DateTime;
use Exception;
use PDO;
use SallePW\SlimApp\Model\Game;
use SallePW\SlimApp\Model\GameRepository;

final class MySQLGameRepository implements GameRepository
{

    public const DATE_FORMAT = 'Y-m-d H:i:s';


    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    private function checkGame(int $gameId, float $price): bool {
        $query = <<<'QUERY'
            SELECT * FROM Game WHERE gameId=:id and ROUND(price, 2)=:price
            QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('id', $gameId, PDO::PARAM_STR);
        $statement->bindParam('price', $price, PDO::PARAM_STR);

        $statement->execute();
        $res = $statement->fetch();

        return $res == false;
    }

    public function addBoughtGame(Game $game, int $userId): bool
    {
        try {
            if (!$this->checkGame($game->getGameId(), $game->getPrice())) {
                error_log(print_r("what", true));
            }

            $gameId = $game->getGameId();
            $title = $game->getTitle();
            $price = $game->getPrice();
            $getThumbnail = $game->getThumbnail();
            $getMetacritireleaseDatecStor = $game->getMetacritireleaseDatecStore();
            $getReleaseDate = $game->getReleaseDate()->format(self::DATE_FORMAT);

            if ($this->checkGame($game->getGameId(), $game->getPrice())) {
                error_log(print_r("is innn", true));

                $query = <<<'QUERY'
                INSERT INTO Game(gameId, titol, price, thumbnail, metacriticStore, releaseDate)
                VALUES(:gameId, :titol, :price, :thumbnail, :metacriticStore, :releaseDate)
                QUERY;

                $statement = $this->database->connection()->prepare($query);

                $statement->bindParam('gameId', $gameId, PDO::PARAM_STR);
                $statement->bindParam('titol', $title, PDO::PARAM_STR);
                $statement->bindParam('price', $price, PDO::PARAM_STR);
                $statement->bindParam('thumbnail', $getThumbnail, PDO::PARAM_STR);
                $statement->bindParam('metacriticStore', $getMetacritireleaseDatecStor, PDO::PARAM_STR);
                $statement->bindParam('releaseDate', $getReleaseDate, PDO::PARAM_STR);

                $statement->execute();
                error_log(print_r("8", true));

            }

            // TODO: Check que execute no peta
            error_log(print_r("10", true));
            $query = <<<'QUERY'
            INSERT INTO ownedGames(gameId, userId)
            SELECT id, :userId as userId
            from Game
            WHERE gameId = :gameId and ROUND(price, 2) = :price
            QUERY;

            $statement = $this->database->connection()->prepare($query);
            error_log(print_r("12", true));
            $statement->bindParam('gameId', $gameId, PDO::PARAM_STR);
            $statement->bindParam('price', $price, PDO::PARAM_STR);
            $statement->bindParam('userId', $userId, PDO::PARAM_STR);

            $statement->execute();
            error_log(print_r("14", true));
            return true;
        } catch (Exception $e) {
            error_log(print_r($e->getMessage(),true));
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

    public function getOwnedGames(int $userId): array {

        $query = <<<'QUERY'
        SELECT * 
        FROM ownedGames, Game 
        WHERE ownedGames.userId =:id AND Game.id = ownedGames.gameId;
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('id', $userId, PDO::PARAM_STR);

        $statement->execute();

        $games = [];
        while(true) {
            $res = $statement->fetch();
            if (!$res) break;
            array_push($games, new Game($res['titol'], (int)$res['gameId'], (float)$res['price'], $res['thumbnail'],  (int)$res['metacriticStore'], new DateTime($res['releaseDate']), true));
        }

        return $games;
    }
}