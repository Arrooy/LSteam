<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Repository;

use PDO;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\UserRepository;

use Exception;

final class MySQLUserRepository implements UserRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function getId(string $email, string $password): int{
        $query = <<< 'QUERY'
        SELECT * FROM users WHERE email=:email
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


    
    // Mira si un usuari existeix a la taula d'usuraris
    // verificats basant-se en el email.
    public function emailExists(String $email) : bool{
        $query = <<< 'QUERY'
        SELECT * FROM users WHERE email=:email
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('email', $email, PDO::PARAM_STR);

        $statement->execute();
        $res = $statement->fetch();

        return $res != false;
    }

    // Mira si un usuari existeix a la taula d'usuraris
    // verificats basant-se en el username.
    public function usernameExists(String $username) : bool{
        $query = <<< 'QUERY'
        SELECT * FROM users WHERE email=:email
        QUERY;

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('email', $email, PDO::PARAM_STR);

        $statement->execute();
        $res = $statement->fetch();

        return $res != false;
    }

    // Mira si un token existeix en la taula de pending users
    public function getPendingUser(string $token) : ?User{
        $query = <<< 'QUERY'
        SELECT * FROM usersPending WHERE token=:token
        QUERY;
        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('token', $token, PDO::PARAM_STR);

        $statement->execute();
        $res = $statement->fetch();

        if ($res != false) return NULL;

        return new User(
            $res['username'],
            $res['email'],
            $res['password'],
            $res['birthday'],
            $res['email'],
        );
    }

    public function saveUser(User $user): void {
        // Check if user already exists.
        if($this->emailExists($user)) {
            throw new Exception('This email is already used!');
        }

        $query = <<<'QUERY'
        INSERT INTO users(username, email, password, birthday, phone)
        VALUES(:username, :email, :password, :birthday, :phone)
        QUERY;
        
        $statement = $this->database->connection()->prepare($query);

        $username = $user->getUsername();
        $email = $user->email();
        $password = $user->password();
        $birthday = $user->getBirthday()->format(self::DATE_FORMAT);
        $phone = $user->getPhone();

        $statement->bindParam('username', $username, PDO::PARAM_STR);
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->bindParam('birthday', $birthday, PDO::PARAM_STR);
        $statement->bindParam('phone', $phone, PDO::PARAM_STR);

        $statement->execute();
    }

    public function savePendingUser(User $user): void {
        // Check if user already exists.
        if($this->emailExists($user)) {
            throw new Exception('This email is already used!');
        }

        $query = <<<'QUERY'
        INSERT INTO usersPending(username, email, password, birthday, phone)
        VALUES(:username, :email, :password, :birthday, :phone)
        QUERY;

        $statement = $this->database->connection()->prepare($query);

        $username = $user->getUsername();
        $email = $user->email();
        $password = $user->password();
        $birthady = $user->getBirthday()->format(self::DATE_FORMAT);
        $phone = $user->getPhone();

        $statement->bindParam('username', $username, PDO::PARAM_STR);
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->bindParam('birthday', $birthady, PDO::PARAM_STR);
        $statement->bindParam('phone', $phone, PDO::PARAM_STR);

        $statement->execute();
    }
}