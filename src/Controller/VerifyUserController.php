<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use SallePW\SlimApp\Model\UserRepository;
use SallePW\SlimApp\Model\UserSaveRepository;

use Slim\Views\Twig;
use Slim\Routing\RouteContext;


final class VerifyUserController {

    public function __construct(private Twig $twig, private UserRepository $userRepository){}

    public function verifyUser(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        //TODO: verificar que funcioni
        $token = $request->getQueryParams()['token'];
        $isSuccess = $this->userRepository->verifyUser($token);

        if ($isSuccess){
            $message = "User confirmation done! Check your inbox to complete the registration and earn 50€!";
        }else{
            $message = "Error! Impossible to verify the user. Maybe you are already verified?";
        }
        return $this->twig->render(
            $response,
            'verifyUser.twig',
            [
                'isSuccess' => $isSuccess,
                'message' => $message,
                // Hrefs de la base
                'log_in_href' => $routeParser->urlFor('login'),
                'sign_up_href' => $routeParser->urlFor('register'),
                'home_href' => $routeParser->urlFor('home')
            ]
        );
    }
}