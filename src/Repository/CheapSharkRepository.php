<?php


namespace SallePW\SlimApp\Repository;


use GuzzleHttp\Client;
use SallePW\SlimApp\Model\Game;
use SallePW\SlimApp\Model\GameRepository;

class CheapSharkRepository implements GameRepository
{
    private Client $client;
    private static ?CheapSharkRepository $instance = null;

    private function __construct(){
        $this->client = new Client();
    }

    public static function getInstance():CheapSharkRepository{
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getDeals() : array{
        $res = $this->client->request('GET', 'https://www.cheapshark.com/api/1.0/deals',
            [
//                'query' => [
//                    'q' => $query,
            ]);
        # Decodifiquem el body
        $jsonResponse = json_decode($res->getBody()->getContents(), true);

        $games = [];

        # Guardem els resultats que ens interesen.
        foreach ($jsonResponse as $game) {
            array_push($games, new Game($game['title'],
                $game['gameID'],
                $game['normalPrice'],
                $game['thumb']));
        }

        return $games;
    }

}