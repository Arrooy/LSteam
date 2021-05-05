<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use DateInterval;
use DateTime;
use Error;
use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Ramsey\Uuid\Uuid;
use SallePW\SlimApp\Model\FriendsRepository;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\UserRepository;
use SallePW\SlimApp\Repository\MySQLFriendsRepository;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

final class FriendsConroller {

    public function __construct(private Twig $twig, private UserRepository $userRepository, private FriendsRepository $friendsRepository) {}

    private function print(string $msg) {
        error_log(print_r($msg, TRUE));
    }

    public function show(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $friends = $this->friendsRepository->getFriends($_SESSION['id'], MySQLFriendsRepository::REQUEST_ACCEPTED);

        return $this->twig->render($response, 'friends.twig', [
            'friendList' => $friends,
            'listTitle' => 'Friend list',
            'isRequests' => false,
            'emptyMessage' => "You don't have any friend yet!",

            'requests_href' => $routeParser->urlFor('friendRequests'),
            'sendRequest_href' => $routeParser->urlFor('sendRequest'),

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

    public function showRequests(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $friends = $this->friendsRepository->getFriends($_SESSION['id'], MySQLFriendsRepository::REQUEST_PENDING);

        return $this->twig->render($response, 'friends.twig', [
            'friendList' => $friends,
            'listTitle' => 'Friend requests',
            'isRequests' => true,
            'emptyMessage' => "It seems that you don't have any friend request to handle",

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

    public function showRequestCreation(Request $request, Response $response, array $error): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $this->twig->render($response, 'sendRequest.twig', [
            'error' => $error,

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

    public function handleSendRequest(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $friendId = -1;
        $error = $this->checkSendRequest($data, $friendId);

        if (!$error) {
            $this->friendsRepository->newRequest($_SESSION['id'], $friendId);
            return $response
                ->withHeader('Location', $routeParser->urlFor("friends"))
                ->withStatus(301);
        }
        else return $this->showRequestCreation($request, $response, $error);
    }

    private function checkSendRequest($data, int &$friendId): array {
        $error = [];
        try {
            $friendId = $this->userRepository->getIdByUsername($data['newFriend']);
            $errorId = $this->friendsRepository->friendCheck($_SESSION['id'], $friendId);

            if ($friendId == $_SESSION['id']) {
                $error['requestError'] = "You cannot send a friend request to yourself!";
            } elseif ($errorId == 0) {
                $error['requestError'] = "This request is already made. You will have to wait for " . $data['newFriend'] . " to answer";
            } elseif ($errorId == 1) {
                $error['requestError'] =  $data['newFriend'] . " is already your friend!";
            }

        } catch (Exception $e) {
            $error['requestError'] =  "Error! There isn't any user with this username";
        }
        return $error;
    }
}