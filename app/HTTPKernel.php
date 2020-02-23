<?php

namespace App;

use Slim\Middleware\MethodOverrideMiddleware;
use Cradle\ViewCompiler;

/**
 * This kernel serves HTTP requests.
 */
class HTTPKernel extends Kernel
{
    /** @var array $middlewarAPP_URLe The middleware classes to be registered for all routes for every request. */
    protected $middlewares = [
        MethodOverrideMiddleware::class,
    ];

    /** @var string $routesFile The path(s) to the routes file(s) to be used by the kernel. */
    protected $routesFiles = [
        'api.php',
        'web.php',
    ];

    /**
     * To be called before app handling is done.
     * To be defined in a kernel.
     * 
     * @return void
     */
    protected function boot(): void
    {
        // Set default parameters for views.
        /** @var ViewCompiler */
        $viewCompiler = $this->app->getContainer()->get('view');
        $viewCompiler->setDefaultParameter('app_url', getenv('APP_URL'));
    }
}
