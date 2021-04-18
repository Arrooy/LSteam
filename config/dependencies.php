<?php
declare(strict_types=1);

use DI\Container;
use Psr\Container\ContainerInterface;

use SallePW\SlimApp\Controller\VerifyUserController;
use Slim\Views\Twig;

use SallePW\SlimApp\Controller\LogInController;
use SallePW\SlimApp\Controller\SearchController;
use SallePW\SlimApp\Controller\RegisterController;
use SallePW\SlimApp\Controller\LandingController;

use SallePW\SlimApp\Repository\PDOSingleton;
use SallePW\SlimApp\Repository\MySQLUserRepository;
use SallePW\SlimApp\Repository\MySQLUserSaveRepository;

use SallePW\SlimApp\Model\UserRepository;

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

$container->set(
    UserRepository::class,
    function (ContainerInterface $container) {
    return new MySQLUserRepository($container->get('db'));
});

$container->set(
    LogInController::class,
    function (Container $c) {
        return new LogInController($c->get("view"),$c->get(UserRepository::class));
    }
);

$container->set(
    RegisterController::class,
    function (Container $c) {
        return new RegisterController($c->get("view"),$c->get(UserRepository::class));
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
        return new VerifyUserController($c->get("view"), $c->get(UserRepository::class));
    }
);
