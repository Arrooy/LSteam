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

        $errors = [];

        $money = $this->userRepository->getMoney($_SESSION['id']);

        return $this->twig->render(
            $response,
            'wallet.twig',
            [
                'money' => number_format($money,2,',','.'),

                'is_user_logged' => isset($_SESSION['id']),
                'errors' => $errors,
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

        $errors = [];


        $curr_money = $this->userRepository->getMoney($_SESSION['id']);
        $add_value = $data['money'];

        if ($add_value != "" && !is_numeric($add_value)){
            $errors['isNumeric'] = true;
            // Mai pasara. no fa falta implemetar la ui.
        }else{

            $errors['positiveVal'] = ($add_value <= 0);

            if(!$errors['positiveVal']){
                $curr_money += $add_value;
                $this->userRepository->setMoney($_SESSION['id'], $curr_money);
            }

        }
        return $this->twig->render(
            $response,
            'wallet.twig',
            [
                'money' => number_format($curr_money,2,',','.'),
                
                'errors' => $errors,

                'is_user_logged' => isset($_SESSION['id']),
                'profilePic' => (!isset($_SESSION['profilePic']) ? "" : $routeParser->urlFor('home') . $_SESSION['profilePic']),

                // Hrefs de la base
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
}