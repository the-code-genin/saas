<?php
namespace App\Controllers\Api;

use App\Helpers\Api;
use Cradle\Controller;
use Psr\Http\Message\ServerRequestInterface;

/**
 * API Controller for misc routes.
 */
class Home extends Controller
{
    /**
     * The index route.
     * 
     * @return array
     */
    protected function index(ServerRequestInterface $request, object $params): array
    {
        return Api::generateErrorResponse(404, 'NotFoundError', 'The resource you requested for was not found.');
    }
}
