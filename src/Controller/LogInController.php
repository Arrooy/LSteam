<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;
 
use SallePW\SlimApp\Controller\GenericFormController;

use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use SallePW\SlimApp\Model\UserRepository;
use SallePW\SlimApp\Model\User;

use Exception;
use DateTime;

final class LogInController extends GenericFormController
{
    public function __construct(private Twig $twig, private UserRepository $userRepository,
    private Messages $flash) {
        parent::__construct($twig,$userRepository,true, $flash);
    }

    public function show(Request $request, Response $response): Response {
        return parent::showForm($request,$response,"handle-login","LogIn","Login",[]);
    }

    public function handleFormSubmission(Request $request, Response $response): Response {
        $errors = parent::checkForm($request);
        if(!empty($errors))
            return parent::showForm($request,$response,"handle-login","LogIn","Login",$errors);

        try {
            $data = $request->getParsedBody();
            $result = $this->userRepository->getId($data['email'], $data['password']);

            $user = $this->userRepository->getUser($result);

            $_SESSION['id'] = $result;

            error_log(print_r($user->getProfilePic(), true));
            $_SESSION['profilePic'] = ProfileController::UPLOADS_DIR . DIRECTORY_SEPARATOR . $user->getProfilePic();
            error_log(print_r($_SESSION['profilePic'], true));
            
        } catch (Exception $exception) {

            $errors['password'] = 'Error: ' . $exception->getMessage();
            return parent::showForm($request,$response,"handle-login","LogIn","Login",$errors);
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        // Redirect a Search.
        return $response
        ->withHeader('Location',$routeParser->urlFor("store"))
        ->withStatus(301);
    }
}
