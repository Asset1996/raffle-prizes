<?php

/**
 * Load dependencies.
 */
require 'vendor/autoload.php';

use App\Controllers\Auth;
use App\Controllers\Prize;
use App\Controllers\User;
use App\Controllers\Admin;

defined('DOTENV_PATH') ?: define('DOTENV_PATH', './');

if (file_exists(DOTENV_PATH . '.env'))
{
    $dotenv = Dotenv\Dotenv::createImmutable(DOTENV_PATH);
    $dotenv->load(true);
}else{
    exit(ROOT.' .env not found');
}

error_reporting(E_ALL);

$app = new Core\Router();

$app->post('/login', [Auth::class, 'login']);
$app->get('/get-current-user', [User::class, 'getCurrentUser']);

$app->get('/get_random_prize', [Prize::class, 'getRandomPrize']);
$app->post('/accept-prize', [Prize::class, 'acceptPrize']);
$app->post('/reject-prize', [Prize::class, 'rejectPrize']);

$app->get('/admin/get-all-accepted-prizes', [Admin::class, 'getAllAcceptedPrizes']);
$app->post('/admin/send-prize', [Admin::class, 'sendPrize']);

$app->run($_SERVER['REQUEST_URI']);