<?php


namespace SallePW\SlimApp\Repository;


use SallePW\SlimApp\Model\Game;
use SallePW\SlimApp\Model\GameRepository;

class CachingCheapSharkRepository implements GameRepository
{
    public function __construct(private GameRepository $repository, private Cache $cache){}

    public function getDeals(): array
    {
        // Pull the games out of cache, if it exists...
        return $this->cache->remember('deals.all', 60, function() {
            // If cache has expired, grab the games out of the API
            return $this->repository->getDeals();
        });
    }

    public function getGame(int $gameId): Game
    {
        // TODO: Implement getGame() method.
    }
}