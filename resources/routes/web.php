<?php

use Slim\App;
use App\Controllers\Web\Home;

/**
 * Routing rules are defined here.
 * 
 * @var App $app
 */

$app->get('/', Home::class.':index');
$app->get('/user/verify/{token: .+}', Home::class.':verifyUserAccount');
