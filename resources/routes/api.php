<?php

use Slim\App;
use App\Controllers\Api\Home;
use App\Controllers\Api\OrganizationCategories;
use App\Middleware\CORSMiddleware;
use App\Middleware\JSONBodyParser;
use Slim\Routing\RouteCollectorProxy;

/**
 * Routing rules are defined here.
 * 
 * @var App $app
 */

$app->group('/api/v1', function (RouteCollectorProxy $group) {

    // Auth Routes.
    $group->post('/user/login', Home::class.':login');
    $group->post('/user/signup', Home::class.':signup');
    $group->get('/user', Home::class.':getLoggedInUser');


    // Organizations end points.
    $group->group('/organizations', function(RouteCollectorProxy $group) {

        // Organization categories endpoints.
        $group->group('/categories', function(RouteCollectorProxy $group) {
            $group->get('[/[{id:\d*}]]', OrganizationCategories::class.':index');
            $group->post('', OrganizationCategories::class.':create');
            $group->map(['PUT', 'PATCH'], '/{id:\d+}', OrganizationCategories::class.':update');
            $group->delete('/{id:\d+}', OrganizationCategories::class.':delete');
        });
    });

    // 404 error catcher.
    $group->any('[{any:.*}]', Home::class.':index');
})->add(new CORSMiddleware)
->add(new JSONBodyParser);
