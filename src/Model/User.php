<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Model;

use DateTime;

final class User
{
    public function __construct(
        private string $username,
        private string $email,
        private string $password,
        private DateTime $birthday,
        private string $phone
        //TODO: afegir imatge
    ) {}

    public function email(): string {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return DateTime
     */
    public function getBirthday(): DateTime
    {
        return $this->birthday;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }
}