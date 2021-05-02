<?php


namespace SallePW\SlimApp\Controller;



use Psr\Http\Message\ResponseInterface as Response;

use SallePW\SlimApp\Model\CheapSharkRepository;
use SallePW\SlimApp\Model\GameRepository;
use SallePW\SlimApp\Model\UserRepository;
use Slim\Psr7\Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Slim\Flash\Messages;

class StoreController
{
    public function __construct(private Twig $twig,
    private UserRepository $userRepository,
    private CheapSharkRepository $cheapSharkRepository,
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
                'formTitle' => "LSteam Store",
                'formSubtitle' => "Wellcome to store! These are the 60 best deals:",

                'flash_messages' => $messages['buy-error'] ?? [],

                'game_deals' => $this->cheapSharkRepository->getDeals(),
                'is_user_logged' => isset($_SESSION['id']),

                'buyAction' => $routeParser->urlFor('handle-store-buy',['gameId' => 1]),

                'profilePic' => $_SESSION['profilePic'],
                'log_in_href' => $routeParser->urlFor('login'),
                'log_out_href' => $routeParser->urlFor('logOut'),
                'sign_up_href' => $routeParser->urlFor('register'),
                'home_href' => $routeParser->urlFor('home'),
                'store_href' =>  $routeParser->urlFor('store'),
                'profile_href' =>  $routeParser->urlFor('profile'),
                'wallet_href' => $routeParser->urlFor('getWallet'),
            ]
        );
    }

    public function buy(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if(isset($_SESSION['id'])){
            $gameId = basename($request->getUri());
            $game = $this->cheapSharkRepository->getGame($gameId);

            $resulting_money = $this->userRepository->getMoney($_SESSION['id']) - $game->getPrice();

            if ($resulting_money >= 0) {
                $this->userRepository->setMoney($_SESSION['id'], $resulting_money);
                $this->gameRepository->addBoughtGame($game, (int)$_SESSION['id']);
            }else{
                $this->flash->addMessage('buy-error',"Error: There is not enough money in your wallet to buy that item. You need " . $resulting_money * -1 . " coins");
            }
        }else{
            $this->flash->addMessage('buy-error',"Error: You are not logged in. Please login!");
        }

        return $response
            ->withHeader('Location', $routeParser->urlFor("store"))
            ->withStatus(302);
    }

    public function myGames(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $games = $this->gameRepository->getBoughtGames($_SESSION['id']);

        return $this->twig->render(
            $response,
            'store.twig',
            [

                'formTitle' => "All my games",
                'formSubtitle' => "There are all the games you own. Great choice!",

                'game_deals' => $games,
                'is_user_logged' => isset($_SESSION['id']),

                'buyAction' => $routeParser->urlFor('handle-store-buy',['gameId' => 1]),

                'log_in_href' => $routeParser->urlFor('login'),
                'log_out_href' => $routeParser->urlFor('logOut'),
                'sign_up_href' => $routeParser->urlFor('register'),
                'home_href' => $routeParser->urlFor('home'),
                'store_href' =>  $routeParser->urlFor('store'),
                'profile_href' =>  $routeParser->urlFor('profile'),
            ]
        );
    }
}