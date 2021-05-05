<?php


namespace SallePW\SlimApp\Repository;


use SallePW\SlimApp\Controller\Cache;
use SallePW\SlimApp\Model\Game;
use SallePW\SlimApp\Model\CheapSharkRepository;

class CachingCheapSharkRepository implements CheapSharkRepository
{
    const CACHE_TIMEOUT_SECONDS = 60;

    public function __construct(private CheapSharkRepository $repository, private Cache $cache){}

    public function getDeals(): array
    {
        // Pull the games out of cache, if it exists...
        return $this->cache->remember('deals.all', $this::CACHE_TIMEOUT_SECONDS, function() {
            // If cache has expired, grab the games out of the API
            return $this->repository->getDeals();
        });
    }

    public function getGame(int $gameId): Game
    {
        // Pull the games out of cache, if it exists...
        return $this->cache->remember('game.'.$gameId, $this::CACHE_TIMEOUT_SECONDS, function() use ($gameId) {
            // If cache has expired, grab the games out of the API
            return $this->repository->getGame($gameId);
        })[0];
    }

    public function getGamesFromIds(array $game_ids): array
    {
        // Pull the games out of cache, if it exists...
        return $this->cache->remember('wished_games.'.implode(',',$game_ids), $this::CACHE_TIMEOUT_SECONDS, function() use ($game_ids) {
            // If cache has expired, grab the games out of the API
            return $this->repository->getGamesFromIds($game_ids);
        });
    }
}