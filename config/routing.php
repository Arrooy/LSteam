<?php

declare(strict_types=1);

use SallePW\SlimApp\Controller\ChangePasswordController;
use SallePW\SlimApp\Controller\LogOutController;
use SallePW\SlimApp\Controller\ProfileController;
use SallePW\SlimApp\Controller\RegisterController;
use SallePW\SlimApp\Controller\LogInController;
use SallePW\SlimApp\Controller\SearchController;
use SallePW\SlimApp\Controller\LandingController;
use SallePW\SlimApp\Controller\WalletController;

use SallePW\SlimApp\Controller\StoreController;
use SallePW\SlimApp\Controller\VerifyUserController;
use SallePW\SlimApp\Middleware\StartSessionMiddleware;
use SallePW\SlimApp\Middleware\VerifySessionMiddleware;

$app->add(StartSessionMiddleware::class);

$app->get(
    '/login',
    LogInController::class . ":show"
)->setName('login');

$app->post(
    '/login',
    LogInController::class . ":handleFormSubmission"
)->setName('handle-login');

$app->get(
    '/register',
    RegisterController::class . ":show"
)->setName('register');

$app->post(
    '/register',
    RegisterController::class . ":handleFormSubmission"
)->setName('handle-register');

$app->get(
    '/activate',
    VerifyUserController::class . ":verifyUser"
)->setName('verify');

$app->post(
    '/logOut',
    LogOutController::class . ":handle_log_out"
)->setName('logOut');

$app->get(
    '/profile',
    ProfileController::class . ":show"
)->setName('profile')->add($app->getContainer()->get('verifySessionMiddleware'));

$app->post(
    '/profile',
    ProfileController::class . ":handleUpdate"
)->setName('profileUpdate')->add($app->getContainer()->get('verifySessionMiddleware'));

$app->get(
    '/',
    LandingController::class . ":show"
)->setName('home');

$app->get(
    '/store',
    StoreController::class . ":show"
)->setName('store');

$app->post(
    '/store/buy/{gameId}',
    StoreController::class . ":buy"
)->setName('handle-store-buy');

$app->get(
    '/profile/changePassword',
    ChangePasswordController::class . ":show"
)->setName('changePassword');

$app->post(
    '/profile/changePassword',
    ChangePasswordController::class . ":handleUpdate"
)->setName('changePasswordUpdate');

$app->get(
    '/user/wallet',
    WalletController::class . ":show"
)->setName('getWallet');

$app->post(
    '/user/wallet',
    WalletController::class . ":handleUpdate"
)->setName('postWallet');

