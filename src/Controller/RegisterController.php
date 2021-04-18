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

final class RegisterController extends GenericFormController
{
    public function __construct(private Twig $twig,
        private UserRepository $userRepository) 
    {
        parent::__construct($twig, false);
    }

    public function show(Request $request, Response $response): Response
    {
        return parent::showForm($request,$response,"handle-register","Register","Register",[]);
    }

    public function handleFormSubmission(Request $request, Response $response): Response
    {
        $errors = parent::checkForm($request);
        
        if(!empty($errors)){
            return parent::showForm($request,$response,"handle-register","Register","Register",$errors);
        }

        try {
            $data = $request->getParsedBody();
            
            $user = new User(
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                new DateTime()
            );
            
            $this->userRepository->save($user);

        } catch (Exception $exception) {
            //  Email used or db exception. 
            $errors['email'] = 'Error: ' . $exception->getMessage();
            return parent::showForm($request,$response,"handle-register","Register","Register",$errors);
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        
        // Redirect a Search.
        return $response
        ->withHeader('Location', $routeParser->urlFor("home"))
        ->withStatus(301);
    }
}
