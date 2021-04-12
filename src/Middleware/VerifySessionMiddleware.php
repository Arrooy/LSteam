<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Slim\Routing\RouteContext;

final class VerifySessionMiddleware
{
    public function __invoke(Request $request, RequestHandler $next): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        
        session_start();

        if (session_status() != PHP_SESSION_ACTIVE || !isset($_SESSION['id'])){    

            return $next->handle($request)
            ->withHeader('Location', $routeParser->urlFor('home'))
            ->withStatus(302);
        }

        return $next->handle($request);
    }
}