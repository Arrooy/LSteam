<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use SallePW\SlimApp\Model\UserSaveRepository;

use Slim\Views\Twig;
use Slim\Routing\RouteContext;
use GuzzleHttp\Client;


final class LandingController
{

    public function __construct(private Twig $twig){}

    public function show(Request $request, Response $response): Response
    {   
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        return $this->twig->render(
            $response,
            'landing.twig',
            [
                'is_login' => isset($_SESSION['id']),
                'log_in_href' => $routeParser->urlFor('login'),
                'log_out_href' => $routeParser->urlFor('logOut'),
                'sign_up_href' => $routeParser->urlFor('register'),
                'profile_href' => $routeParser->urlFor('profile'),
                'home_href' => $routeParser->urlFor('home')
            ]
        );
    }
}