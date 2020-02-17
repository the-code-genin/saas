<?php
namespace App\Middleware;

use Cradle\MiddleWare;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * This middleware parses the request body and sets it in the request to be handled by a controller.
 */
class JSONBodyParser extends MiddleWare
{
	public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $rawBody = $request->getBody()->getContents();
        $parsedBody = json_decode($rawBody);

        if (is_null($parsedBody) || $parsedBody == false) {
            $parsedBody = [];
        }

        $request = $request->withAttribute('body', $parsedBody);

        $response = $handler->handle($request);
        return $response;
    }
}
