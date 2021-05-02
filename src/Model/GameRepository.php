<?php


namespace SallePW\SlimApp\Model;


interface GameRepository
{
    public function addBoughtGame(int $gameId,int $userId): bool;
}