<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

abstract class GenericFormController
{
    public function __construct(private Twig $twig, private bool $is_login){}
    
    protected function showForm(Request $request, Response $response, 
    string $formAction, string $submitValue, string $formTitle, array $errors): Response{
        
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $is_login = $this->is_login;

        return $this->twig->render(
            $response,
            'login_register.twig',
            [
                'formErrors' => $errors,
                'formAction' => $routeParser->urlFor($formAction),
                'formMethod' => "POST",
                'is_login' => $is_login,
                'submitValue' => $submitValue,
                'formTitle' => $formTitle,

                // Hrefs de la base
                'log_in_href' => $routeParser->urlFor('login'),
                'sign_up_href' => $routeParser->urlFor('register'),
                'home_href' => $routeParser->urlFor('home')
            ]
        );
    }

    abstract protected function handleFormSubmission(Request $request, Response $response): Response;

    protected function checkForm(Request $request): array{
        $data = $request->getParsedBody();
        $errors = [];
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) 
        {
            $errors['email'] = 'The email address is not valid';
        }

        if (empty($data['password']) || strlen($data['password']) < 6) 
        {
            $errors['password'] = 'The password must contain at least 6 characters.';
        }
        elseif(!preg_match("#[0-9]+#",$data['password']))
        {
            $errors['password'] = "The password must contain at least 1 number.";
        }
        elseif(!preg_match("#[a-zA-Z]+#",$data['password']))
        {
            $errors['password'] = "The password must contain at least 1 letter.";
        }
        
        error_log(print_r($errors, TRUE)); 

        return $errors;
    }
}