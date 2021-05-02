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

    public function addBoughtGame(int $gameId, int $userId): bool
    {
        try {

        $query = <<<'QUERY'
        INSERT INTO ownedGames(gameId, userId)
        VALUES(:gid, :uid)
        QUERY;

            $statement = $this->database->connection()->prepare($query);
            $statement->bindParam('gid', $gameId, PDO::PARAM_STR);
            $statement->bindParam('uid', $userId, PDO::PARAM_STR);

            $statement->execute();

            //TODO: Verificar que s'ha pogut fer!
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