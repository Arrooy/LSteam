<?php


namespace SallePW\SlimApp\Controller;


use SallePW\SlimApp\Model\GameRepository;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Slim\Psr7\Request;

class WishListController
{
    public function __construct(private Twig $twig,
                                private GameRepository $gameRepository){}

    public function show(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $wishes = $this->gameRepository->getWishedGames($_SESSION['id']);

        return $this->twig->render(
            $response,
            'generic_game_display.twig',
            [
                'formTitle' => "Wishlsit",
                'formSubtitle' => "Showing all saved games:",

                'game_deals' => $wishes,
                'is_user_logged' => isset($_SESSION['id']),

                'isWishlist' => true,
                // Nota: El game id s'ignora aqui. Twig fa repace per al valor correcte.
                'buyAction' => $routeParser->urlFor('handle-store-buy',['gameId' => 1]),

                // Hrefs de la base
                'profilePic' => (!isset($_SESSION['profilePic']) ? "" : $routeParser->urlFor('home') . $_SESSION['profilePic']),
                'log_in_href' => $routeParser->urlFor('login'),
                'log_out_href' => $routeParser->urlFor('logOut'),
                'sign_up_href' => $routeParser->urlFor('register'),
                'profile_href' => $routeParser->urlFor('profile'),
                'home_href' => $routeParser->urlFor('home'),
                'store_href' =>  $routeParser->urlFor('store'),
                'wallet_href' => $routeParser->urlFor('getWallet'),
                'myGames_href' => $routeParser->urlFor('myGames'),
                'wishlist_href' => $routeParser->urlFor('wishlist'),
            ]
        );
    }

    public function showSingleGame(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        // TODO fer el twig.

        $gameId = basename($request->getUri());
        return $this->twig->render(
            $response,
            'single_game.twig',
            [
                'formTitle' => "Wishlsit",

                'is_user_logged' => isset($_SESSION['id']),

                'isWishlist' => true,
                // Nota: El game id s'ignora aqui. Twig fa repace per al valor correcte.
                'buyAction' => $routeParser->urlFor('handle-store-buy',['gameId' => 1]),

                // Hrefs de la base
                'profilePic' => (!isset($_SESSION['profilePic']) ? "" : $routeParser->urlFor('home') . $_SESSION['profilePic']),
                'log_in_href' => $routeParser->urlFor('login'),
                'log_out_href' => $routeParser->urlFor('logOut'),
                'sign_up_href' => $routeParser->urlFor('register'),
                'profile_href' => $routeParser->urlFor('profile'),
                'home_href' => $routeParser->urlFor('home'),
                'store_href' =>  $routeParser->urlFor('store'),
                'wallet_href' => $routeParser->urlFor('getWallet'),
                'myGames_href' => $routeParser->urlFor('myGames'),
                'wishlist_href' => $routeParser->urlFor('wishlist'),
            ]
        );
    }
}