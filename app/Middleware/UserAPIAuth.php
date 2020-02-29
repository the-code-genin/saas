<?php
namespace App\Middleware;

use App\Helpers\Api;
use App\Models\User;
use Carbon\Carbon;
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

    /** @var bool */
    protected $verified;

    /** @var string */
    protected $userType;

    public function __construct(App $app, bool $verified = true, string $userType = '')
    {
        $this->app = $app;
        $this->db = $app->getContainer()->get('db');
        $this->verified = $verified;
        $this->userType = $userType;
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
        $userId = @explode('.', $apiToken, 2)[1];
        $user = User::where('id', $userId)->first();
        if (is_null($user)) { // If no user exists.
            $response = Api::generateErrorResponse(401, 'AuthenticationError', 'Invalid authorization token.');
            return $this->generateResponse($response);
        } else if ($this->db->table('user_api_tokens')->where('token', $apiToken)->where('user_id', $userId)->count() != 1) {
            $response = Api::generateErrorResponse(401, 'AuthenticationError', 'Invalid authorization token.');
            return $this->generateResponse($response);
        } else if ($user->status == 'banned') { // User is banned
            $response = Api::generateErrorResponse(401, 'AuthenticationError', 'User is unable to log in.');
            return $response;
        } else if ($this->verified == true && $user->verified == false) { // If user must be verified to use this route
            $response = Api::generateErrorResponse(401, 'AuthenticationError', 'User must be verified to use this route.');
            return $this->generateResponse($response);
        } else if (!empty($this->userType) && $user->user_type != $this->userType) { // If a particular user type is specified
            $response = Api::generateErrorResponse(401, 'AuthenticationError', 'This user can not access this route.');
            return $this->generateResponse($response);
        }


        // Remove an expired token
        $date = $this->db->table('user_api_tokens')->where('token', $apiToken)->where('user_id', $userId)->first()->created_at;
        $createdAt = Carbon::createFromFormat('Y-m-d H:i:s', $date);
        if (Carbon::now()->diffInDays($createdAt) > 30) { // Expired token
            $this->db->table('user_api_tokens')->where('token', $apiToken)->where('user_id', $userId)->delete();
            $response = Api::generateErrorResponse(401, 'AuthenticationError', 'Expired authorization token.');
            return $this->generateResponse($response);
        }


        // Handle request.
        $request = $request->withAttribute('user', $user)->withAttribute('auth_token', $apiToken);
        $response = $handler->handle($request);
        return $response;
    }
}
