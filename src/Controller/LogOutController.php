<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;
 
use SallePW\SlimApp\Controller\GenericFormController;

use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use SallePW\SlimApp\Model\UserRepository;
use SallePW\SlimApp\Model\User;

use Exception;
use DateTime;

final class LogOutController {
    public function __construct() {}

    public function handle_log_out(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        error_log(print_r("EL ID ES!", TRUE));

//        session_start();
//        error_log(print_r("EL ID ES!", TRUE));
//        error_log(print_r($_SESSION['id'], TRUE));
//        if (session_status() == PHP_SESSION_ACTIVE) {
//            error_log("Bye Bye session!");
//            session_destroy();
//            unset( $_SESSION );
//        }

        return $response
        ->withHeader('Location', $routeParser->urlFor("home"))
        ->withStatus(301);
    }
}
