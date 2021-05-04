<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use SallePW\SlimApp\Model\GifRepository;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\UserRepository;

use Slim\Views\Twig;
use Slim\Routing\RouteContext;

final class VerifyUserController {

    public function __construct(private Twig $twig, private UserRepository $userRepository,
                                private GifRepository $gifRepository){}

    public function verifyUser(Request $request, Response $response): Response {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();


        $token = $request->getQueryParams()['token'];
        $isSuccess = $this->userRepository->verifyUser($token);

        if ($isSuccess){
            $message = "User confirmation done! Check your inbox to complete the registration and earn 50€!";
            $gif_query = "money";
            
            $this->userRepository->setMoney($this->userRepository->getIdByGivenEmail($_SESSION['email']), 50);
            $this->sendEmail($_SESSION['email'],'http://localhost:8030/login');
        }else{
            $message = "Error! Impossible to verify the user. Maybe you are already verified?";
            $gif_query = "sad";
        }
        return $this->twig->render(
            $response,
            'verifyUser.twig',
            [

                'isSuccess' => $isSuccess,
                'message' => $message,
                'is_user_logged' => isset($_SESSION['id']),

                'gif_url' => $this->gifRepository->getRandomGif($gif_query),

                // Hrefs de la base

                // Hrefs de la base
                'profilePic' => (!isset($_SESSION['profilePic']) ? "" : $routeParser->urlFor('home') . $_SESSION['profilePic']),
                'log_in_href' => $routeParser->urlFor('login'),
                'log_out_href' => $routeParser->urlFor('logOut'),
                'sign_up_href' => $routeParser->urlFor('register'),
                'profile_href' => $routeParser->urlFor('profile'),
                'home_href' => $routeParser->urlFor('home'),
                'store_href' =>  $routeParser->urlFor('store'),
                'wallet_href' => $routeParser->urlFor('getWallet'),
                'myGames_href' => $routeParser->urlFor('myGames'),
                'wishlist_href' => $routeParser->urlFor('wishlist'),
            ]
        );
    }

    public function sendEmail(String $email, String $base): void{

        $mail = new PHPMailer(true);

        try {
            //Code settings
//            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                //Enable verbose debug output
            $mail->isSMTP();                                      //Send using SMTP
            $mail->Host       = 'mail.smtpbucket.com';            //Set the SMTP server to send through
            $mail->Port       = 8025;                              //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom('lsteam@lsteam.com', 'LSTEAM BACKEND TEAM');
            $mail->addAddress($email);
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'We added 50$ into your account! Open this link to start in LSteam';

            //Generate the link to send in the email to activate
            $mail->Body    = 'Click this link to start the xperience! <a href="' . $base . '"> Link</a>';
            $mail->AltBody = 'Click this link to start the xperience! <a href="' . $base . '"> Link</a>';

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}