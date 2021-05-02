<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Repository;

use PDO;
use SallePW\SlimApp\Model\FriendsRepository;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\UserRepository;

use Exception;

use DateTime;
final class MySQLFriendsRepository implements FriendsRepository {

    public int $REQUEST_PENDING = 0;
    public int $REQUEST_ACCEPTED = 1;
    public int $REQUEST_DECLINED = 2;

    private PDOSingleton $database;
    public function __construct(PDOSingleton $database) {
        $this->database = $database;
    }

    public function getFriends(int $user, int $state): array {
        $query = <<<'QUERY'
        SELECT id, username, email, birthday, phone
        FROM users as u
        INNER JOIN friendRequests fr ON (u.id = fr.id_orig or u.id = fr.id_dest)
        WHERE u.id != :id and fr.state = :state;
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('id', $user, PDO::PARAM_STR);
        $statement->bindParam('state', $state, PDO::PARAM_STR);

        $statement->execute();

        $friends = [];
        while (true) {
            $res = $statement->fetch();
            if (!$res) break;

            array_push($friends, new User(
                $res['id'],
                $res['username'],
                $res['email'],
                "",
                $res['birthday'],
                $res['phone']
            ));
        }

        return [];
    }

    public function newRequest(int $orig, int $dest) {
        $query = <<<'QUERY'
        INSERT INTO friendRequests(id_orig, id_dest)
        VALUES(:id_orig, :id_dest)
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('id_orig', $orig, PDO::PARAM_STR);
        $statement->bindParam('id_dest', $dest, PDO::PARAM_STR);

        $statement->execute();
    }

    public function updateRequest(int $orig, int $dest, int $state) {
        $query = <<<'QUERY'
        UPDATE friendRequests
        SET state=:state, accept_time=CURRENT_TIMESTAMP
        WHERE id_orig=:orig and id_dest=:dest
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('orig', $orig, PDO::PARAM_STR);
        $statement->bindParam('dest', $dest, PDO::PARAM_STR);
        $statement->bindParam('state', $state, PDO::PARAM_STR);

        $statement->execute();
    }
}