<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use DateInterval;
use DateTime;
use Error;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Ramsey\Uuid\Uuid;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\UserRepository;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

final class ChangePasswordController {
    public const DATE_FORMAT = 'Y-m-d';
    private const UPLOADS_DIR = '../uploads';

    private const DEFAULT_IMG = 'default.jpg';

    private const UNEXPECTED_ERROR = "An unexpected error occurred uploading the file '%s'...";
    private const INVALID_EXTENSION_ERROR = "The received file extension '%s' is not valid";
    private const INVALID_SIZE_ERROR = "The file must be under 1MB";
    private const INVALID_DIMENSIONS_ERROR = "The file must be 500x500 pixels";
    private const TOO_MANY_FILES_ERROR = "Only one file can be uploaded!";

    private const ALLOWED_EXTENSIONS = ['jpg', 'png'];

    public function __construct(private Twig $twig, private UserRepository $userRepository) {}

    private function print(string $msg) {
        error_log(print_r($msg, TRUE));
    }

    public function show(Request $request, Response $response, array $errors): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        return $this->twig->render($response, 'changePassword.twig', [
            'formErrors' => $errors,

            'formData' => $request->getParsedBody(),
            'formAction' => $routeParser->urlFor("changePassword"),
            'formMethod' => "POST",
            'is_user_logged' => isset($_SESSION['id']),
            'submitValue' => "Change",
            'formTitle' => "Change password",

            // Hrefs de la base
            'log_in_href' => $routeParser->urlFor('login'),
            'log_out_href' => $routeParser->urlFor('logOut'),
            'sign_up_href' => $routeParser->urlFor('register'),
            'profile_href' => $routeParser->urlFor('profile'),
            'home_href' => $routeParser->urlFor('home')
        ]);
    }

    public function handleUpdate(Request $request, Response $response): Response {
        $user = $this->userRepository->getUser($_SESSION['id']);
        $data = $request->getParsedBody();

        $errors = $this->checkPassword($data);

        if (empty($errors)) {
            $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
            $this->userRepository->updateUser($user);

            //TODO: Posar algo que informi de si ha anat be el canvi de pswd

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $response->withHeader("Location", $routeParser->urlFor('profile'))->withStatus(302);
        }

        return $this->show($request, $response, $errors);
    }

    private function checkPassword(array $data) : array{
        $errors = [];

        if (empty($data['password']) || strlen($data['password']) <= 6)
            $errors['password'] = 'The password must contain at least 7 characters.';

        elseif(!(preg_match('/[A-Z]/', $data['password']) && preg_match('/[a-z]/', $data['password'])))
            $errors['password'] = "The password must contain at least 1 uppercase and 1 lowercase.";

        elseif(!preg_match("#[0-9]+#",$data['password']))
            $errors['password'] = "The password must contain at least 1 number.";

        if( $data['password'] != $data['password_repeat'])
            $errors['password_repeat'] = "Passwords must match";

        return $errors;
    }
}