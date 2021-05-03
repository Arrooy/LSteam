<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;
 
use SallePW\SlimApp\Controller\GenericFormController;

use SallePW\SlimApp\Model\GifRepository;
use Slim\Flash\Messages;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use SallePW\SlimApp\Model\UserRepository;
use SallePW\SlimApp\Model\User;

use DateTime;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Exception;

final class RegisterController extends GenericFormController
{
    public function __construct(private Twig $twig,
        private UserRepository $userRepository,
        private GifRepository $gifRepository,
                                private Messages $flash)
    {
        parent::__construct($twig, $userRepository, false,$flash);
    }

    public function show(Request $request, Response $response): Response
    {
        return parent::showForm($request, $response,"handle-register","Register","Register",[]);
    }

    public function handleFormSubmission(Request $request, Response $response): Response {

        //checks errors of register Data
        $errors = parent::checkForm($request);

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        
        if(!empty($errors)){
            return parent::showForm($request, $response,"handle-register","Register","Register",$errors);
        }

        try {
            $data = $request->getParsedBody();
            
            $user = new User(
                0,
                $data['username'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                new DateTime($data['birthday']),
                $data['phone'],
            );

            $this->userRepository->savePendingUser($user);

            $base = 'http://localhost:8030/activate';//$routeParser->urlFor('verify');

            $_SESSION['email'] = $user->email();

            //We send the email to the User
            $this->sendEmail($user, $base);

        } catch (Exception $exception) {
            //  Email used or db exception. (No pasa mai si tot va be aka email funciona i bbdd pot guardar usuari)

            /*
             *  TODO: plantejarse si posar aqui un error del servidor rollo 502 enlloc de un twig.
             *   Potser es mes adecuat al ser un error intern...
             * */

            $errors['email'] = 'Error: ' . $exception->getMessage();
            return parent::showForm($request,$response,"handle-register","Register","Register",$errors);
        }

        // Mostrem vista register done.
        return $this->twig->render(
            $response,
            'register_done.twig',
            [
                'user_email' => $user->email(),
                'gif_url' => $this->gifRepository->getRandomGif("success"),
                'formTitle' => "Register",


                // Hrefs de la base
                'profilePic' => (!isset($_SESSION['profilePic']) ? "" : $routeParser->urlFor('home') . $_SESSION['profilePic']),
                'log_in_href' => $routeParser->urlFor('login'),
                'log_out_href' => $routeParser->urlFor('logOut'),
                'sign_up_href' => $routeParser->urlFor('register'),
                'profile_href' => $routeParser->urlFor('profile'),
                'home_href' => $routeParser->urlFor('home'),
                'friends_href' =>  $routeParser->urlFor('friends'),
                'store_href' =>  $routeParser->urlFor('store'),
                'wallet_href' => $routeParser->urlFor('getWallet'),
                'myGames_href' => $routeParser->urlFor('myGames'),
            ]
        );
    }
    public function sendEmail(User $user, String $base): void{

        $mail = new PHPMailer(true);

        try {
            //Server settings
//            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                //Enable verbose debug output
            $mail->isSMTP();                                      //Send using SMTP
            $mail->Host       = 'mail.smtpbucket.com';            //Set the SMTP server to send through
            $mail->Port       = 8025;                              //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom('lsteam@lsteam.com', 'LSTEAM BACKEND TEAM');
            $mail->addAddress($user->email(), $user->getUsername());
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Activation LSteam';

            //Generate the link to send in the email to activate
            $mail->Body    = 'Click this link to verify! <a href="' . $base . '?token=' . $this->userRepository->getUserToken($user) . '"> Link</a>';
            $mail->AltBody = 'Click this link to verify! <a href="' . $base . '?token=' . $this->userRepository->getUserToken($user) . '"> Link</a>';

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}

