<?php

namespace SallePW\SlimApp\Controller;

use SallePW\SlimApp\Model\CheapSharkRepository;
use SallePW\SlimApp\Model\GameRepository;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Slim\Psr7\Request;

class WishListController
{
    public function __construct(private Twig $twig,
                                private GameRepository $gameRepository,
                                private CheapSharkRepository $cheapSharkRepository){}

    public function show(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $wishedGame_ids = $this->gameRepository->getWishedGamesIds($_SESSION['id']);

        if(!empty($wishedGame_ids)){
            $wishes = $this->cheapSharkRepository->getGamesFromIds($wishedGame_ids);
        }

        return $this->twig->render(
            $response,
            'generic_game_display.twig',
            [
                'formTitle' => "Wishlist",
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

    public function addWish(Request $request, Response $response): Response {
        $gameId = basename($request->getUri());
        error_log("ADDING GAME " .  print_r($gameId,true));

        // TODO: verificar que sigui el codi adecuat.
        // TODO: Posar els codis adecuats a la resta.
        if($this->gameIsAlreadyBought($gameId)) {
            $response->withStatus(403);
        }

        $this->gameRepository->addWishedGame($gameId,$_SESSION['id']);
        return $response->withStatus(200);
    }

    public function deleteWish(Request $request, Response $response): Response {
        $gameId = basename($request->getUri());
        error_log("DELETING GAME " .  print_r($gameId,true));

        // TODO: verificar que sigui el codi adecuat.
        // TODO: Posar els codis adecuats a la resta.
        if($this->gameIsAlreadyBought($gameId)) {
            $response->withStatus(403);
        }
        $this->gameRepository->removeWishedGame($gameId,$_SESSION['id']);
        return $response->withStatus(200);
    }

    private function gameIsAlreadyBought(int $new_game_id){
        // Revisem que no estigui comprat!
        $bought_games = $this->gameRepository->getBoughtGamesIds($_SESSION['id']);

        foreach ($bought_games as $bought_game_id) {
            if (strcmp($bought_game_id, $new_game_id) == 0) {
                return true;
            }
        }

        return false;
    }

}