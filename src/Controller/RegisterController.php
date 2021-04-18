<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;
 
use SallePW\SlimApp\Controller\GenericFormController;

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
use PHPMailer\PHPMailer\Exception;

final class RegisterController extends GenericFormController
{
    public function __construct(private Twig $twig,
        private UserRepository $userRepository) 
    {
        parent::__construct($twig, $userRepository, false);
    }

    public function show(Request $request, Response $response): Response
    {
        return parent::showForm($request,$response,"handle-register","Register","Register",[]);
    }

    public function handleFormSubmission(Request $request, Response $response): Response {

        //checks errors of register Data
        $errors = parent::checkForm($request);
        
        if(!empty($errors)){
            return parent::showForm($request, $response,"handle-register","Register","Register",$errors);
        }

        try {
            $data = $request->getParsedBody();
            
            $user = new User(
                $data['username'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                new DateTime($data['birthday']),
                $data['phone'],
            );

            $this->userRepository->savePendingUser($user);

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

            $base = 'http://localhost:8030/activate';//$routeParser->urlFor('verify');

            //We send the email to the User
            $this->sendEmail($user, $base);

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

