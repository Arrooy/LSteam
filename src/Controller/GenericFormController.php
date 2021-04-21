<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use DateInterval;
use SallePW\SlimApp\Model\UserRepository;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use DateTime;
abstract class GenericFormController
{
    public function __construct(private Twig $twig,
                                private UserRepository $userRepository,
                                private bool $is_login){}

    protected function showForm(Request $request, Response $response,
    string $formAction, string $submitValue, string $formTitle, array $errors): Response{

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $this->twig->render(
            $response,
            'login_register.twig', [
                'formData' => $request->getParsedBody(),
                'formErrors' => $errors,
                'formAction' => $routeParser->urlFor($formAction),
                'formMethod' => "POST",
                'is_login' => $this->is_login,
                'submitValue' => $submitValue,
                'formTitle' => $formTitle,

                // Hrefs de la base
                'log_in_href' => $routeParser->urlFor('login'),
                'log_out_href' => $routeParser->urlFor('logOut'),
                'sign_up_href' => $routeParser->urlFor('register'),
                'profile_href' => $routeParser->urlFor('profile'),
                'home_href' => $routeParser->urlFor('home')
            ]
        );
    }

    abstract protected function handleFormSubmission(Request $request, Response $response): Response;

    protected function checkForm(Request $request): array{
        $data = $request->getParsedBody();
        $errors = [];

        

        if (empty($data['password']) || strlen($data['password']) <= 6)
        {
            $errors['password'] = 'The password must contain at least 7 characters.';
        }

        elseif(!(preg_match('/[A-Z]/', $data['password']) && preg_match('/[a-z]/', $data['password'])))
        {
            $errors['password'] = "The password must contain at least 1 uppercase and 1 lowercase.";
        }

        elseif(!preg_match("#[0-9]+#",$data['password']))
        {
            $errors['password'] = "The password must contain at least 1 number.";
        }

        
        if($this->is_login == false){
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL))
            {
                $errors['email'] = 'The email address is not valid';
            }elseif(!(str_contains($data['email'], '@salle.url.edu') || str_contains($data['email'], '@students.salle.url.edu'))) {
                $errors['email'] = 'The email domain not accepted. Try using a @salle.url.edu or students.salle.url.edu domain';
            }elseif($this->userRepository->emailExists($data['email'])){
                $errors['email'] = 'The email address is already used';
            }



            
            if(!ctype_alnum($data['username']))
            {
                $errors['username'] = 'The username is not valid';
            }elseif($this->userRepository->usernameExists($data['username']))
            {
                $errors['username'] = 'The username already exists';
            }
            if( $data['password'] != $data['password_repeat']){
                $errors['password_repeat'] = "Passwords must match";
            }
            if(!empty($data['phone'] && (mb_strlen($data['phone'], "utf8") != 9 || ($data['phone'][0] != 6 && $data['phone'][0] != 7) || ($data['phone'][0] == 7 && $data['phone'][1] == 0))))
            {
                $errors['phone'] = "The phone number is not valid.";
            }

            // Es crea objecte de dateTime
            $bday= new DateTime($data['birthday']);
            // Afegim 18 anys
            $bday->add(new DateInterval("P18Y"));

            // Mirem si la data supera l'actual per saber si és major d'edat
            if($bday >= new DateTime()){
                $errors['birthday'] = "You must be over 18 to register";
            }
        }

//        error_log(print_r($errors, TRUE));

        return $errors;
    }
}