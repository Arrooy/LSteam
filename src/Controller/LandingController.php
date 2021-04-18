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
    private Twig $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function show(Request $request, Response $response): Response
    {   
        //$routeParser = RouteContext::fromRequest($request)->getRouteParser();
        return $this->twig->render(
        //return $this->container->get('view')->render(
            $response,
            'landing.twig',
            [
                
            ]
        );
    }

    public function handleLanding(Request $request, Response $response): Response 
    {
        //$routeParser = RouteContext::fromRequest($request)->getRouteParser();

        //$data = $request->getParsedBody();
                
        
        return $this->twig->render(
        //return $this->container->get('view')->render(
            $response,
            'langing.twig',
            [           
            ]
        );
    }
}