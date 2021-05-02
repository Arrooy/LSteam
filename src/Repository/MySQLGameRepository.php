<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Repository;

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

    public function addBoughtGame(Game $game, int $userId): bool
    {
        try {
            // TODO: Nomes fer el insert si el gameId no es troba a la taula.
            $query = <<<'QUERY'
            INSERT INTO Game(gameId, titol, price, thumbnail, metacriticStore, releaseDate)
            VALUES(:gameId, :titol, :price, :thumbnail, :metacriticStore, :releaseDate)
            QUERY;

            $statement = $this->database->connection()->prepare($query);

            $gameId = $game->getGameId();
            $title = $game->getTitle();
            $price = $game->getPrice();
            $getThumbnail = $game->getThumbnail();
            $getMetacritireleaseDatecStor = $game->getMetacritireleaseDatecStore();
            $getReleaseDate = $game->getReleaseDate()->format(self::DATE_FORMAT);;

            $statement->bindParam('gameId', $gameId , PDO::PARAM_STR);
            $statement->bindParam('titol', $title, PDO::PARAM_STR);
            $statement->bindParam('price', $price, PDO::PARAM_STR);
            $statement->bindParam('thumbnail',$getThumbnail , PDO::PARAM_STR);
            $statement->bindParam('metacriticStore', $getMetacritireleaseDatecStor, PDO::PARAM_STR);
            $statement->bindParam('releaseDate', $getReleaseDate, PDO::PARAM_STR);


            $statement->execute();

            // TODO: Check que execute no peta

            $query = <<<'QUERY'
            INSERT INTO ownedGames(gameId, userId)
            VALUES(:gameId, :userId)
            QUERY;

            $statement = $this->database->connection()->prepare($query);

            $statement->bindParam('gameId', $gameId, PDO::PARAM_STR);
            $statement->bindParam('userId', $userId, PDO::PARAM_STR);

            $statement->execute();
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
        error_log(print_r($userId, TRUE));
        $query = <<<'QUERY'
        SELECT * FROM ownedGames WHERE userId =:id;
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('id', $userId, PDO::PARAM_STR);

        $statement->execute();

        $games = [];
        while(true) {
            $res = $statement->fetch();
            if (!$res) break;
            //TODO: posar parametres correctament
            array_push($games, new Game("", $res['gameId'], -1, "", -1, null, true));
        }

        return $games;
    }
}