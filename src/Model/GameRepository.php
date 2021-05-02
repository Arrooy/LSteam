<?php


namespace SallePW\SlimApp\Model;


interface GameRepository
{
    public function addBoughtGame(Game $game, int $userId): bool;
    public function getBoughtGames(int $userId): array;
}