<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Model;

interface UserRepository
{
    public function saveUser(User $user): void;
    public function verifyUser(string $token): bool;
    public function getId(string $email, string $password): int;
}