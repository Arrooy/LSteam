<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Slim\Views\Twig;
use Slim\Routing\RouteContext;
use GuzzleHttp\Client;


final class WalletController
{

    public function __construct(private Twig $twig){}

    public function show(Request $request, Response $response): Response
    {   
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();


        return $this->twig->render(
            $response,
            'wallet.twig',
            [

            ]
        );
    }
}