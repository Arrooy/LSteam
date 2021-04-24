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

        if (session_status() != PHP_SESSION_ACTIVE || !isset($_SESSION['id'])){
            header('Location: ' . $routeParser->urlFor('home'));
            exit();
        }

        return $next->handle($request);
    }
}