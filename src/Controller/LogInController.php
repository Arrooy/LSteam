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

final class LogInController extends GenericFormController
{
    public function __construct(private Twig $twig, private UserRepository $userRepository) {
        parent::__construct($twig,$userRepository,true);
    }

    public function show(Request $request, Response $response): Response
    {
        return parent::showForm($request,$response,"handle-login","LogIn","Login",[]);
    }

    public function handleFormSubmission(Request $request, Response $response): Response
    {

        $errors = parent::checkForm($request);
        
        if(!empty($errors)){
            return parent::showForm($request,$response,"handle-login","LogIn","Login",$errors);
        }

        try {
            $data = $request->getParsedBody();
            //TODO: Aplicar el hash al password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT),
            $result = $this->userRepository->getId($data['email'], $hashedPassword);
            
        } catch (Exception $exception) {
            $errors['password'] = 'Error: ' . $exception->getMessage();
            return parent::showForm($request,$response,"handle-login","LogIn","Login",$errors);
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        
        // S'inicia la sessió de l'usuari.
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }  

        $_SESSION['id'] = $result;

        // Redirect a Search.
        return $response
        ->withHeader('Location',$routeParser->urlFor("search"))
        ->withStatus(301);
    }
}
