<?php


namespace SallePW\SlimApp\Repository;


use GuzzleHttp\Client;
use SallePW\SlimApp\Model\Deal;
use SallePW\SlimApp\Model\DetailedGame;
use SallePW\SlimApp\Model\Game;
use SallePW\SlimApp\Model\CheapSharkRepository;

class API_CheapSharkRepository implements CheapSharkRepository
{
    private Client $client;
    private static ?API_CheapSharkRepository $instance = null;

    private function __construct(){
        $this->client = new Client();
    }

    public static function getInstance():API_CheapSharkRepository{
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getDeals() : array{

        $res = $this->client->request('GET', 'https://www.cheapshark.com/api/1.0/deals',
            [
                'query' => [
                    'storeID' => '1',
                    ]
            ]);

        # Decodifiquem el body
        $jsonResponse = json_decode($res->getBody()->getContents(), true);

        $games = [];

        # Guardem els resultats que ens interesen.
        foreach ($jsonResponse as $game) {

            //Processem el thumbnail per aconseguir la versio augmentada.
            $bigger_thumbnail = $this->tryGetBiggerThumbnail($game['thumb']);

            array_push($games, new Game($game['title'],
                $game['gameID'],
                $game['normalPrice'],
                $bigger_thumbnail));
        }

        return $games;
    }

    public function getGame(int $gameId): Game
    {
        // TODO: Implement getGame() method.
    }

    // Donada una llista de games, retorna l'informacio d'aquets de l'API.
    public function getGamesByIds(array $game_ids): array
    {
        $res = $this->client->request('GET', 'https://www.cheapshark.com/api/1.0/games',
            [
                'query' => [
                    'ids' => implode(",", $game_ids),
                ]
            ]);

        # Decodifiquem el body
        $jsonResponse = json_decode($res->getBody()->getContents(), true);

        $games = [];

        # Guardem els resultats que ens interesen.
        foreach ($jsonResponse as $gameId => $game) {

            error_log(print_r($game_ids,true));

//            $deals = [];
//
//            foreach ($game['deals'] as $deal){
//
//                array_push($deals,new Deal());
//            }

            //Processem el thumbnail per aconseguir la versio augmentada.
            $bigger_thumbnail = $this->tryGetBiggerThumbnail($game['info']['thumb']);

            array_push($games, new DetailedGame($game['info']['title'],
                $gameId,
                0.0,
                $bigger_thumbnail,
                $game['cheapestPriceEver']['price'],
                [],
            ));
        }

        return $games;
    }


    private function tryGetBiggerThumbnail(string $thumb): string
    {
        $parsed_thumb = parse_url($thumb);

        if ($parsed_thumb['host'] == 'cdn.cloudflare.steamstatic.com') {

            // Sabem que es pot millorar la calitat de l'imatge modificant la url!
            $newPath = implode('/',explode('/',$parsed_thumb['path'],-1));
            $parsed_thumb['path'] = $newPath . "/header.jpg";
            return $this->unparse_url($parsed_thumb);

        }elseif($parsed_thumb['host'] == 'images-na.ssl-images-amazon.com') {
            // Sabem que es pot millorar la calitat de l'imatge modificant la url!
            $parsed_thumb['path'] = substr($parsed_thumb['path'],0,-12) . ".jpg";
            return $this->unparse_url($parsed_thumb);
        }else{
            // No hi ha forma de aconseguir una foto millor (sense agafar dades d'unaltre api)
            return $thumb;
        }
    }

    private function unparse_url($parsed_url): string
    {
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = $parsed_url['host'] ?? '';
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user     = $parsed_url['user'] ?? '';
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = $parsed_url['path'] ?? '';
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }

}