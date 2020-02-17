<?php

use Slim\App;
use App\Controllers\Api\Home;
use App\Middleware\UserAPIAuth;
use App\Middleware\CORSMiddleware;
use App\Middleware\JSONBodyParser;
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\Api\OrganizationCategories;

/**
 * Routing rules are defined here.
 * 
 * @var App $app
 */

$app->group('/api/v1', function (RouteCollectorProxy $group) use ($app) {

    // Auth Routes.
    $group->post('/user/login', Home::class.':login');
    $group->post('/user/signup', Home::class.':signup');
    $group->get('/user', Home::class.':getLoggedInUser')->add(new UserAPIAuth($app));


    // Organizations end points.
    $group->group('/organizations', function(RouteCollectorProxy $group) use ($app) {

        // Organization categories endpoints.
        $group->group('/categories', function(RouteCollectorProxy $group) use ($app) {
            $group->get('[/[{id:\d*}]]', OrganizationCategories::class.':index');

            $group->group('', function(RouteCollectorProxy $group) {
                $group->post('', OrganizationCategories::class.':create');
                $group->map(['PUT', 'PATCH'], '/{id:\d+}', OrganizationCategories::class.':update');
                $group->delete('/{id:\d+}', OrganizationCategories::class.':delete');
            })->add(new UserAPIAuth($app));
        });
    });


    // 404 error catcher.
    $group->any('[{any:.*}]', Home::class.':index');
})->add(new CORSMiddleware)->add(new JSONBodyParser);
