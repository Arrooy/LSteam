<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use SallePW\SlimApp\Model\UserRepository;

use Slim\Views\Twig;
use Slim\Routing\RouteContext;
use GuzzleHttp\Client;


final class WalletController
{

    public function __construct(private Twig $twig, private UserRepository $userRepository){}

    public function show(Request $request, Response $response): Response
    {   
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $data['money'] = $this->userRepository->getMoney($_SESSION['id']);

        return $this->twig->render(
            $response,
            'wallet.twig',
            [
                'info' => $data,

                'is_user_logged' => isset($_SESSION['id']),

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
            ]
        );
    }

    public function handleUpdate(Request $request, Response $response): Response
    {   
        $data = $request->getParsedBody();

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        
        $error = ($data['money'] <= 0);

        if(!$error){
            $money = $this->userRepository->getMoney($_SESSION['id']);
            $data['money'] += $money;
            $this->userRepository->setMoney($_SESSION['id'], $data['money']);
        }else{
            $data['money'] = $this->userRepository->getMoney($_SESSION['id']);
        }
        
        return $this->twig->render(
            $response,
            'wallet.twig',
            [
                'info' => $data,
                
                'ammount' => $error,

                'is_user_logged' => isset($_SESSION['id']),
                'profilePic' => (!isset($_SESSION['profilePic']) ? "" : $_SESSION['profilePic']),

                //href base
                'log_in_href' => $routeParser->urlFor('login'),
                'log_out_href' => $routeParser->urlFor('logOut'),
                'sign_up_href' => $routeParser->urlFor('register'),
                'profile_href' => $routeParser->urlFor('profile'),
                'home_href' => $routeParser->urlFor('home'),
                'store_href' =>  $routeParser->urlFor('store'),
                'wallet_href' => $routeParser->urlFor('getWallet'),
            ]
        );
    }
}