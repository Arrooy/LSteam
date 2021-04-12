<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Model;

interface UserSaveRepository
{
    public function addSearch(int $userId, string $searchText): void;
}