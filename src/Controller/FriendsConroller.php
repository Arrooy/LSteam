<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use DateInterval;
use DateTime;
use Error;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Ramsey\Uuid\Uuid;
use SallePW\SlimApp\Model\FriendsRepository;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\UserRepository;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

final class FriendsConroller {

    public function __construct(private Twig $twig, private FriendsRepository $friendsRepository) {}

    private function print(string $msg) {
        error_log(print_r($msg, TRUE));
    }

    public function show(Request $request, Response $response, array $errors): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $this->twig->render($response, 'friends.twig', [

            // Hrefs de la base
            'is_user_logged' => isset($_SESSION['id']),
            'profilePic' => (!isset($_SESSION['profilePic']) ? "" : $routeParser->urlFor('home') . $_SESSION['profilePic']),
            'log_in_href' => $routeParser->urlFor('login'),
            'log_out_href' => $routeParser->urlFor('logOut'),
            'sign_up_href' => $routeParser->urlFor('register'),
            'profile_href' => $routeParser->urlFor('profile'),
            'home_href' => $routeParser->urlFor('home'),
            'store_href' =>  $routeParser->urlFor('store'),
            'friends_href' =>  $routeParser->urlFor('friends'),
            'wallet_href' => $routeParser->urlFor('getWallet'),
            'myGames_href' => $routeParser->urlFor('myGames'),
        ]);
    }


}