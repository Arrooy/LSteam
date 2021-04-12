<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Repository;

use PDO;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\UserRepository;

use Exception;

final class MysqlUserRepository implements UserRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function getId(string $email, string $password): int{
        $query = <<< 'QUERY'
        SELECT * FROM User WHERE email=:email
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('email', $email, PDO::PARAM_STR);

        $statement->execute();
        $res = $statement->fetch();
        

        if(!$res){
            throw new Exception('User not found!');
        }else if($res['password'] != $password){
            throw new Exception('Incorrect password!');
        }

        return (int)$res['id'];
    }
    
    // Mira si un usuari existeix basant-se en el email. 
    public function exists(User $user) : bool{
        $query = <<< 'QUERY'
        SELECT * FROM User WHERE email=:email
        QUERY;
        $email = $user->email();
        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('email', $email, PDO::PARAM_STR);

        $statement->execute();
        $res = $statement->fetch();

        return $res != false;
    }

    public function save(User $user): void
    {

        // Check if user already exists.
        if($this->exists($user)){
            throw new Exception('This email is already used!');
        }

        $query = <<<'QUERY'
        INSERT INTO User(email, password, created_at)
        VALUES(:email, :password, :created_at)
        QUERY;
        
        $statement = $this->database->connection()->prepare($query);

        $email = $user->email();
        $password = $user->password();
        $createdAt = $user->createdAt()->format(self::DATE_FORMAT);

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->bindParam('created_at', $createdAt, PDO::PARAM_STR);

        $statement->execute();
    }
}