<?php


namespace SallePW\SlimApp\Controller;


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use SallePW\SlimApp\Model\GameRepository;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class StoreController
{
    public function __construct(private Twig $twig,
    private GameRepository $gameRepository){}

    public function show(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();



        return $this->twig->render(
            $response,
            'store.twig',
            [
                'game_deals' => $this->gameRepository->getDeals(),
                'is_login' => isset($_SESSION['id']),
                'log_in_href' => $routeParser->urlFor('login'),
                'log_out_href' => $routeParser->urlFor('logOut'),
                'sign_up_href' => $routeParser->urlFor('register'),
                'home_href' => $routeParser->urlFor('home')
            ]
        );
    }
}