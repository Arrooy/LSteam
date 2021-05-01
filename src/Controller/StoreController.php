<?php


namespace SallePW\SlimApp\Controller;



use Psr\Http\Message\ResponseInterface as Response;

use SallePW\SlimApp\Model\GameRepository;
use Slim\Psr7\Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Slim\Flash\Messages;

class StoreController
{
    public function __construct(private Twig $twig,
    private GameRepository $gameRepository,
    private Messages $flash){}

    public function show(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $messages = $this->flash->getMessages();

        return $this->twig->render(
            $response,
            'store.twig',
            [
                'flash_messages' => $messages['buy-error'] ?? [],

                'game_deals' => $this->gameRepository->getDeals(),
                'is_user_logged' => isset($_SESSION['id']),

                'buyAction' => $routeParser->urlFor('handle-store-buy',['gameId' => 1]),

                'log_in_href' => $routeParser->urlFor('login'),
                'log_out_href' => $routeParser->urlFor('logOut'),
                'sign_up_href' => $routeParser->urlFor('register'),
                'home_href' => $routeParser->urlFor('home'),
                'store_href' =>  $routeParser->urlFor('store'),
            ]
        );
    }

    public function buy(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $this->flash->addMessage('buy-error',"There is not enough money in your wallet to buy that item");

        $gameId = basename($request->getUri());
        // TODO: Game id trewballara mb aixlo. Potser posar nom a message!

        return $response
            ->withHeader('Location', $routeParser->urlFor("store"))
            ->withStatus(302);
    }
}