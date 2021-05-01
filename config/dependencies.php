<?php
declare(strict_types=1);

use DI\Container;
use Psr\Container\ContainerInterface;

use SallePW\SlimApp\Controller\LogOutController;
use SallePW\SlimApp\Controller\StoreController;
use SallePW\SlimApp\Controller\VerifyUserController;
use SallePW\SlimApp\Repository\CachingCheapSharkRepository;
use SallePW\SlimApp\Repository\CheapSharkRepository;
use SallePW\SlimApp\Repository\GIF;
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

$container->set('gif_db', function () {
    return GIF::getInstance($_ENV['GIPHY_API_KEY']);
});

$container->set('game_db', function (Container $c) {
    return CheapSharkRepository::getInstance();
//    return new CachingCheapSharkRepository($cheapSharkRepo, "['cache.store']);
});

$container->set(
    'flash',
    function () {
        return new Messages();
    }
);

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
        return new RegisterController($c->get("view"),$c->get(UserRepository::class), $c->get('gif_db'));
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
        return new VerifyUserController($c->get("view"), $c->get(UserRepository::class), $c->get('gif_db'));
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
        return new StoreController($c->get('view'),$c->get('game_db'), $c->get('flash'));
    }
);
