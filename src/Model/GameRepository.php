<?php


namespace SallePW\SlimApp\Model;


interface GameRepository
{
    public function getDeals() : array;
    public function getGame(int $gameId): Game;
}