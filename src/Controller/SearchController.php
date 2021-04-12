<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use SallePW\SlimApp\Model\UserSaveRepository;

use Slim\Views\Twig;
use Slim\Routing\RouteContext;
use GuzzleHttp\Client;


final class SearchController
{
    public function __construct(private Twig $twig,
    private UserSaveRepository $userSavesRepository){}

    // If the user session is up, it shows the search form. Else it redirects to login site.
    public function show(Request $request, Response $response): Response
    {   
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
          
        return $this->twig->render(
            $response,
            'search.twig',
            [
                'formAction' => $routeParser->urlFor('handle-search'),
                'formLogOutAction' => $routeParser->urlFor('handle-logOut'),
            ]
        );
    }

    // Logs out the user removing its session.
    public function logOut(Request $request, Response $response): Response {
        session_start();
        if (session_status() == PHP_SESSION_ACTIVE) {
            error_log("Bye Bye session!");
            session_destroy();
            unset( $_SESSION );
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        return $response
            ->withHeader('Location', $routeParser->urlFor("home"))
            ->withStatus(302);
    }

    //  Manage the youtube search and display its results
    public function handleSearch(Request $request, Response $response): Response 
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $data = $request->getParsedBody();
        
        $videos = [];
        
        // Afegim a la db la cerca.
        $this->userSavesRepository->addSearch($_SESSION['id'],$data["searchQuery"]);

        // Preparem el client HTTP i fem el GET a l'API de youtube
        $client = new Client();
        $res = $client->request('GET', 'https://www.googleapis.com/youtube/v3/search',
        [
           'query' => ['part' => 'snippet',
                        'videoEmbeddable' => 'true',
                        'order' => 'viewCount',
                        'q' => $data["searchQuery"],
                        'maxResults' => '15',
                        'type' => 'video',
                        'videoDefinition' => 'high',
                        'key' => $_ENV['YOUTUBE_API_KEY']]
        ]);
        
        # Decodifiquem el body
        $jsonResponse = json_decode($res->getBody()->getContents(), true);
        
        # Guardem els resultats que ens interesen.
        foreach ($jsonResponse['items'] as $video) {
            array_push($videos,["title"=>$video['snippet']['title'],"link"=>$video['id']['videoId']]);
        }

        //Pintem la vista amb els videos trobats a youtube.
        return $this->twig->render(
            $response,
            'search.twig',
            [
                'videos' => $videos,
                'formAction' => $routeParser->urlFor('handle-search'),
                'formLogOutAction' => $routeParser->urlFor('handle-logOut'),            
            ]
        );
    }
}