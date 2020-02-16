<?php

use Slim\App;
use App\Controllers\Api\Home;
use App\Controllers\Api\OrganizationCategories;
use App\Middleware\CORSMiddleware;
use Slim\Routing\RouteCollectorProxy;

/**
 * Routing rules are defined here.
 * 
 * @var App $app
 */

$app->group('/api/v1', function (RouteCollectorProxy $group) {

    // Organizations end point.
    $group->group('/organizations', function(RouteCollectorProxy $group) {

        // Organization categories endpoint.
        $group->group('/categories', function(RouteCollectorProxy $group) {
            $group->get('[/{id:\d*}]', OrganizationCategories::class.':index');
            $group->post('', OrganizationCategories::class.':create');
            $group->map(['PUT', 'PATCH'], '/{id:\d+}', OrganizationCategories::class.':update');
            $group->delete('/{id:\d+}', OrganizationCategories::class.':delete');
        });
    });

    $group->any('[{any:.*}]', Home::class.':index');
})->add(new CORSMiddleware);
