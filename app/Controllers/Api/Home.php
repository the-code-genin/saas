<?php
namespace App\Controllers\Api;

use Cradle\Controller;
use Psr\Http\Message\ServerRequestInterface;

class Home extends Controller
{
    /**
     * The index route.
     * 
     * @return array
     */
    protected function index(ServerRequestInterface $request, object $params)
    {
        return [
            'success' => false,
            'error' => [
                'code' => 404,
                'type' => 'NotFoundError',
                'message' => 'The api route you requested for was not found.',
            ]
        ];
    }
}
