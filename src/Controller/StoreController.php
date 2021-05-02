<?php


namespace SallePW\SlimApp\Controller;



use Psr\Http\Message\ResponseInterface as Response;

use SallePW\SlimApp\Model\CheapSharkRepository;
use SallePW\SlimApp\Model\GameRepository;
use Slim\Psr7\Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Slim\Flash\Messages;

class StoreController
{
    public function __construct(private Twig $twig,
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
            $have_money = true;
            if ( $have_money ) {
                $gameId = basename($request->getUri());
                $this->gameRepository->addBoughtGame((int)$gameId,(int)$_SESSION['id']);
            }else{
                $this->flash->addMessage('buy-error',"Error: There is not enough money in your wallet to buy that item");
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

    }
}