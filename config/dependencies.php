<?php
declare(strict_types=1);

use DI\Container;
use Psr\Container\ContainerInterface;

use SallePW\SlimApp\Controller\ChangePasswordController;
use SallePW\SlimApp\Controller\LogOutController;
use SallePW\SlimApp\Controller\ProfileController;
use SallePW\SlimApp\Controller\StoreController;
use SallePW\SlimApp\Controller\VerifyUserController;
use SallePW\SlimApp\Middleware\VerifySessionMiddleware;
use SallePW\SlimApp\Model\GameRepository;
use SallePW\SlimApp\Repository\API_CheapSharkRepository;
use SallePW\SlimApp\Repository\API_GifRepository;
use SallePW\SlimApp\Repository\CachingCheapSharkRepository;
use SallePW\SlimApp\Repository\MySQLGameRepository;
use Slim\Views\Twig;

use SallePW\SlimApp\Controller\LogInController;
use SallePW\SlimApp\Controller\RegisterController;
use SallePW\SlimApp\Controller\LandingController;

use SallePW\SlimApp\Repository\PDOSingleton;
use SallePW\SlimApp\Repository\MySQLUserRepository;

use SallePW\SlimApp\Model\UserRepository;
use Slim\Flash\Messages;

$container = new Container();

$container->set(
    'view',
    function () {
        return Twig::create(__DIR__ . '/../templates', ['cache' => false]);
    }
);

$container->set('db', function () {
    return PDOSingleton::getInstance(
        $_ENV['MYSQL_USER'],
        $_ENV['MYSQL_ROOT_PASSWORD'],
        $_ENV['MYSQL_HOST'],
        $_ENV['MYSQL_PORT'],
        $_ENV['MYSQL_DATABASE']
    );
});

$container->set('gif_api', function () {
    return API_GifRepository::getInstance($_ENV['GIPHY_API_KEY']);
});

$container->set('game_api', function (Container $c) {
    return API_CheapSharkRepository::getInstance();
//    return new CachingCheapSharkRepository($cheapSharkRepo, "['cache.store']);
});

$container->set(
    'flash',
    function (ContainerInterface $c) {
        if (session_status() != PHP_SESSION_ACTIVE)
        session_start();
        return new Messages();
    }
);

$container->set(
    'verifySessionMiddleware',
    function (ContainerInterface $c) {
        return new VerifySessionMiddleware($c->get('flash'));
    }
);

$container->set(
UserRepository::class,
function (ContainerInterface $container) {
    return new MySQLUserRepository($container->get('db'));
});

$container->set(
GameRepository::class,
function (ContainerInterface $container) {
    return new MySQLGameRepository($container->get('db'));
});

$container->set(
    LogInController::class,
    function (Container $c) {
        return new LogInController($c->get("view"),$c->get(UserRepository::class), $c->get('flash'));
    }
);

$container->set(
    RegisterController::class,
    function (Container $c) {
        return new RegisterController($c->get("view"),$c->get(UserRepository::class), $c->get('gif_api'), $c->get('flash'));
    }
);

$container->set(
    LandingController::class,
    function (Container $c) {
        return new LandingController($c->get("view"));
    }
);

$container->set(
    VerifyUserController::class,
    function (Container $c) {
        return new VerifyUserController($c->get("view"), $c->get(UserRepository::class), $c->get('gif_api'));
    }
);

$container->set(
    LogOutController::class,
    function (Container $c) {
        return new LogOutController();
    }
);

$container->set(
   StoreController::class,
    function (Container $c) {
        return new StoreController($c->get('view'), $c->get('game_api'),$c->get(GameRepository::class), $c->get('flash'));
    }
);

$container->set(
    ProfileController::class,
    function (Container $c) {
        return new ProfileController($c->get("view"), $c->get(UserRepository::class));
    }
);

$container->set(
    ChangePasswordController::class,
    function (Container $c) {
        return new ChangePasswordController($c->get("view"), $c->get(UserRepository::class));
    }
);
