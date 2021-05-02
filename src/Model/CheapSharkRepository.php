<?php


namespace SallePW\SlimApp\Model;


interface CheapSharkRepository
{
    public function getDeals() : array;
    public function getGame(int $gameId): Game;
    public function getGamesByIds(array $game_ids): array;
}