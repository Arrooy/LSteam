<?php

declare(strict_types=1);

use SallePW\SlimApp\Controller\LogOutController;
use SallePW\SlimApp\Controller\ProfileController;
use SallePW\SlimApp\Controller\RegisterController;
use SallePW\SlimApp\Controller\LogInController;
use SallePW\SlimApp\Controller\SearchController;
use SallePW\SlimApp\Controller\LandingController;

use SallePW\SlimApp\Controller\VerifyUserController;
use SallePW\SlimApp\Middleware\VerifySessionMiddleware;

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
)->setName('profile')->add(VerifySessionMiddleware::class);

$app->post(
    '/profile',
    ProfileController::class . ":handleUpdate"
)->setName('profileUpdate')->add(VerifySessionMiddleware::class);

$app->get(
    '/',
    LandingController::class . ":show"
)->setName('home');

