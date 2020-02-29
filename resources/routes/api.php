<?php

use Slim\App;
use App\Controllers\Api\Home;
use App\Controllers\Api\Jobs;
use App\Middleware\UserAPIAuth;
use App\Middleware\CORSMiddleware;
use App\Middleware\JSONBodyParser;
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\Api\OrganizationCategories;
use App\Controllers\Api\Users;

/**
 * Routing rules are defined here.
 * 
 * @var App $app
 */

$app->group('/api/v1', function (RouteCollectorProxy $group) use ($app) {

    // User routes.
    $group->group('/user', function(RouteCollectorProxy $group) use ($app) {
        $group->post('/login', Users::class.':login');
        $group->post('[/signup]', Users::class.':signup');

        // Semi secure routes.
        $group->group('', function(RouteCollectorProxy $group) {
            $group->get('', Users::class.':index');
        })->add(new UserAPIAuth($app, false));
    });

    // Organizations end points.
    $group->group('', function(RouteCollectorProxy $group) use ($app) {

    });


    // Organization categories endpoints.
    $group->group('/organizations/categories', function(RouteCollectorProxy $group) use ($app) {
        $group->get('[/[{id:\d*}]]', OrganizationCategories::class.':index');

        // Secure routes.
        $group->group('', function(RouteCollectorProxy $group) {
            $group->post('', OrganizationCategories::class.':create');
            $group->map(['PUT', 'PATCH'], '/{id:\d+}', OrganizationCategories::class.':update');
            $group->delete('/{id:\d+}', OrganizationCategories::class.':delete');
        })->add(new UserAPIAuth($app));
    });


    // Job routes.
    $group->group('/jobs', function(RouteCollectorProxy $group) use ($app) {
        $group->post('', Jobs::class.':create')->add(new UserAPIAuth($app, true, 'organization'));
    });


    // 404 error catcher.
    $group->any('[{any:.*}]', Home::class.':index');
})->add(new CORSMiddleware)->add(new JSONBodyParser);
