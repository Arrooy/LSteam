<?php


namespace SallePW\SlimApp\Model;


interface GameRepository
{
    public function getOwnedGames(int $userId): array;

    public function addBoughtGame(Game $game, int $userId): bool;
    public function getBoughtGames(int $userId): array;

    public function addWishedGame(int $gameId, int $userId): bool;
    public function getWishedGames(int $userId): array;
}