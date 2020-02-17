<?php
namespace App\Controllers\Api;

use App\Helpers\Api;
use Cradle\Controller;
use Psr\Http\Message\ServerRequestInterface;

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

    /**
     * Log a user in.
     *
     * @param ServerRequestInterface $request
     * @param object $params
     * @return array
     */
    protected function login(ServerRequestInterface $request, object $params): array
    {
        $payload = [];
        return [
            'success' => true,
            'payload' => $payload
        ];
    }

    /**
     * Sign a user up.
     *
     * @param ServerRequestInterface $request
     * @param object $params
     * @return array
     */
    protected function signup(ServerRequestInterface $request, object $params): array
    {
        $payload = [];
        return [
            'success' => true,
            'payload' => $payload
        ];
    }

    /**
     * Get the current logged in user.
     *
     * @param ServerRequestInterface $request
     * @param object $params
     * @return array
     */
    protected function getLoggedInUser(ServerRequestInterface $request, object $params): array
    {
        $payload = [];
        return [
            'success' => true,
            'payload' => $payload
        ];
    }
}
