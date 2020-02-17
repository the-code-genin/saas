<?php
namespace App\Middleware;

use App\Helpers\Api;
use App\Models\User;
use Slim\App;
use Cradle\MiddleWare;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Illuminate\Database\Capsule\Manager;

/**
 * This middleware authenticates users for the api.
 * It checks for the API authorization token in the header and uses it to validate a user request.
 */
class UserAPIAuth extends MiddleWare
{
    /** @var App */
    protected $app;

    /** @var Manager */
    protected $db;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->db = $app->getContainer()->get('db');
    }

    /**
     * Generate response.
     *
     * @param array $responseArray
     * @return ResponseInterface
     */
    protected function generateResponse(array $responseArray): ResponseInterface
    {
        $response = $this->app->getResponseFactory()->createResponse();
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($responseArray));
        return $response;
    }

	public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Get the api token from header.
        $tokenHeader = $request->getHeader('Authorization');
        if (empty($tokenHeader)) { // If authorization token is not set.
            $response = Api::generateErrorResponse(401, 'AuthenticationError', 'Authorization header not set.');
            return $this->generateResponse($response);
        }

        if (!preg_match('/Bearer (\$\.\d+\.(.+))/i', $tokenHeader[0], $matches)) {
            $response = Api::generateErrorResponse(401, 'AuthenticationError', 'Invalid authorization header.');
            return $this->generateResponse($response);
        }

        // Extract the token.
        $apiToken = $matches[1];


        // Validate token.
        $user_id = explode('.', $apiToken, 2)[1];
        $user = User::where('id', $user_id)->first();
        if (is_null($user)) { // If no user exists.
            $response = Api::generateErrorResponse(401, 'AuthenticationError', 'Invalid authorization header.');
            return $this->generateResponse($response);
        } else if ($this->db->table('user_api_tokens')->where('token', $apiToken)->where('user_id', $user_id)->count() != 1) {
            $response = Api::generateErrorResponse(401, 'AuthenticationError', 'Invalid authorization header.');
            return $this->generateResponse($response);
        }

        // Handle request.
        $request = $request->withAttribute('user', $user);
        $response = $handler->handle($request);
        return $response;
    }
}
