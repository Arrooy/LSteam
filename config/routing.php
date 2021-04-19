<?php

declare(strict_types=1);

use SallePW\SlimApp\Controller\LogOutController;
use SallePW\SlimApp\Controller\RegisterController;
use SallePW\SlimApp\Controller\LogInController;
use SallePW\SlimApp\Controller\SearchController;
use SallePW\SlimApp\Controller\LandingController;

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

//$app->get(
//    '/search',
//    SearchController::class . ":show"
//)->setName('search')->add(VerifySessionMiddleware::class);
//
//$app->post(
//    '/search',
//    SearchController::class . ":handleSearch"
//)->setName('handle-search')->add(VerifySessionMiddleware::class);

$app->get(
    '/activate',
    VerifyUserController::class . ":verifyUser"
)->setName('verify');

$app->post(
    '/logOut',
    LogOutController::class . ":handle_log_out"
)->setName('logOut');

$app->get(
    '/',
    LandingController::class . ":show"
)->setName('home');

