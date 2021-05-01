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
//
//    public function getGameById(int $gameId): Game
//    {
//
//    }
//
//    public function getGamesById(array $ids): array
//    {
//
//    }

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

    public function getGame(int $gameId): Game
    {
        // TODO: Implement getGame() method.
    }
}