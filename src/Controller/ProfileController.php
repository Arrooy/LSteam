<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use DateInterval;
use DateTime;
use Error;
use PDO;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Ramsey\Uuid\Uuid;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\UserRepository;
use SallePW\SlimApp\Repository\MySQLUserRepository;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

final class ProfileController
{

    public const DATE_FORMAT = 'Y-m-d';
    private const UPLOADS_DIR = 'uploads';

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

    public function show(Request $request, Response $response, array $errors = NULL): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $user = $this->userRepository->getUser($_SESSION['id']);

        $profilePic = $user->getProfilePic();
        $profilePic = self::UPLOADS_DIR . DIRECTORY_SEPARATOR ."$profilePic";

        return $this->twig->render($response, 'profile.twig', [
            'errors' => $errors,

            'formData' => $request->getParsedBody(),
            'formErrors' => [],
            'formAction' => $routeParser->urlFor("profile"),
            'formMethod' => "POST",
            'is_login' => isset($_SESSION['id']),
            'submitValue' => "Update profile",
            'formTitle' => "Profile",

            'username' => $user->getUsername(),
            'email' => $user->email(),
            'phone' => $user->getPhone(),
            'birthday' => $user->getBirthday()->format(self::DATE_FORMAT),
            'profilePic' => $profilePic,

            // Hrefs de la base
            'log_in_href' => $routeParser->urlFor('login'),
            'log_out_href' => $routeParser->urlFor('logOut'),
            'sign_up_href' => $routeParser->urlFor('register'),
            'profile_href' => $routeParser->urlFor('profile'),
            'home_href' => $routeParser->urlFor('home')
        ]);
    }

    public function handleUpdate(Request $request, Response $response): Response {
        $uploadedFiles = $request->getUploadedFiles()['files'];
        $user = $this->userRepository->getUser($_SESSION['id']);

        if ($uploadedFiles == NULL) $nFiles = 0;
        else $nFiles = count($uploadedFiles);

        $profilePic = $user->getProfilePic();
        $uploadedFile = array_pop($uploadedFiles);
        $original_name = $uploadedFile->getClientFilename();

        if (!empty($original_name)) {
            if ($uploadedFile->getError() !== UPLOAD_ERR_OK)
                $errors[] = sprintf(
                    self::UNEXPECTED_ERROR,
                    $uploadedFile->getClientFilename()
                );

            $uuid = Uuid::uuid4();
            $name = $uuid->toString();

            $fileInfo = pathinfo($original_name);
            $format = $fileInfo['extension'];
            $img_size = $uploadedFile->getSize();

            if (!in_array($format, self::ALLOWED_EXTENSIONS, true))
                $errors['profilePic'] = sprintf(self::INVALID_EXTENSION_ERROR, $format);

            if ($img_size > pow(2, 20))
                $errors['profilePic'] = sprintf(self::INVALID_SIZE_ERROR, $format);

            try {
                $uploadedFile->moveTo(self::UPLOADS_DIR . DIRECTORY_SEPARATOR . $name . "." . $format);
                $sizeInfo = getimagesize(self::UPLOADS_DIR . DIRECTORY_SEPARATOR . $name . "." . $format);
                if ($sizeInfo[0] != 500 || $sizeInfo[1] != 500)
                    $errors['profilePic'] = sprintf(self::INVALID_DIMENSIONS_ERROR, $format);

                if (!empty($errors)) {
                    if ($profilePic != self::DEFAULT_IMG) unlink(self::UPLOADS_DIR . DIRECTORY_SEPARATOR . $profilePic);
                    $profilePic = $name . "." . $format;
                } else {
                    unlink(self::UPLOADS_DIR . DIRECTORY_SEPARATOR . $name . "." . $format);
                }
            } catch (Error $e) {
                $errors['profilePic'] = $e->getMessage();
            }
        }

        $data = $request->getParsedBody();
        $this->userRepository->updateUser(new User(
            empty($data['username']) ? $user->getUsername() : $data['username'],
            empty($data['email']) ? $user->email() : $data['email'],
            "",
            new DateTime($data['birthday']),
            empty($data['phone']) ? $user->getPhone() : $data['phone'],
            $profilePic
        ));

        if (!empty($errors)) return $this->show($request, $response, $errors);

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        return $response
            ->withHeader('Location', $routeParser->urlFor("profile"))
            ->withStatus(301);
    }

    protected function checkForm(Request $request): array{
        $data = $request->getParsedBody();
        $errors = [];

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL))
            $errors['email'] = 'The email address is not valid';
        elseif(!(str_contains($data['email'], '@salle.url.edu') || str_contains($data['email'], '@students.salle.url.edu')))
            $errors['email'] = 'The email domain not accepted. Try using a @salle.url.edu or students.salle.url.edu domain';
        elseif($this->userRepository->emailExists($data['email']))
            $errors['email'] = 'The email address is already used';

        if(!ctype_alnum($data['username']))
            $errors['username'] = 'The username is not valid';
        elseif($this->userRepository->usernameExists($data['username']))
            $errors['username'] = 'The username already exists';

        if(!empty($data['phone'] && (mb_strlen($data['phone'], "utf8") != 9 || ($data['phone'][0] != 6 && $data['phone'][0] != 7) || ($data['phone'][0] == 7 && $data['phone'][1] == 0))))
            $errors['phone'] = "The phone number is not valid.";

        // Es crea objecte de dateTime
        $bday = new DateTime($data['birthday']);
        // Afegim 18 anys
        $bday->add(new DateInterval("P18Y"));

        // Mirem si la data supera l'actual per saber si és major d'edat
        if($bday >= new DateTime()){
            $errors['birthday'] = "You must be over 18 to register";
        }

        return $errors;
    }
}