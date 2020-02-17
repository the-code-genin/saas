<?php
namespace App\Controllers\Api;

use App\Helpers\Api;
use App\Models\User;
use Cradle\Controller;
use Psr\Http\Message\ServerRequestInterface;
use Valitron\Validator;

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
        $input = $request->getAttribute('body');

        $validator = new Validator((array) $input);
        $validator->rule('required', ['email', 'password']);
        $validator->rule('email', 'email');
        if (!$validator->validate()) { // Validation fails.
            $errors = $validator->errors();
            return Api::generateErrorResponse(401, 'AuthenticationError', array_shift($errors)[0]);
        }

        // Authenticate user by email and password.
        $user = User::where('email', $input->email)->first();
        if (is_null($user)) {
            return Api::generateErrorResponse(401, 'AuthenticationError', 'User email and password do not match.');
        } else if (!password_verify($input->password, $user->password)) {
            return Api::generateErrorResponse(401, 'AuthenticationError', 'User email and password do not match.');
        }

        // Generate and save api token.
        $hex = bin2hex(random_bytes(64));
        $apiToken = '$' . '.' . $user->id . '.' . password_hash($hex, PASSWORD_DEFAULT) . '.' . $hex;

        $this->db->table('user_api_tokens')->insert([
            'user_id' => $user->id,
            'token' => $apiToken,
        ]);

        // Response.
        return [
            'success' => true,
            'payload' => [
                'data' => $user,
                'token' => $apiToken,
            ]
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
