<?php
namespace App\Controllers\Web;

use Cradle\View;
use App\Models\User;
use Cradle\Controller;
use Slim\Exception\HttpNotFoundException;
use Psr\Http\Message\ServerRequestInterface;

class Home extends Controller
{
    /**
     * The index page.
     */
    protected function index(ServerRequestInterface $request, object $params)
    {
        return new View('home.twig');
    }

    /**
     * Verify user account with token
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param object $params
     *
     * @return array
     */
    protected function verifyUserAccount(ServerRequestInterface $request, object $params): void
    {
        $verificationToken = $params->token;
        $query = $this->db->table('user_verification_tokens')->where('token', $verificationToken);

        // Verify token
        if ($query->count() != 1) { // If the token is not found.
            throw new HttpNotFoundException($request);
        }

        // Verify user exists
        $userId = $query->select(['user_id'])->first()->user_id;
        $user = User::where('id', $userId)->where('status', 'pending');
        if ($user->count() != 1) { // If no valid user is not found.
            throw new HttpNotFoundException($request);
        }

        // Mark user as active
        $user = $user->first();
        $user->status = 'active';
        $user->save();

        // Redirect users to their dashboard on the frontend.
        $this->setHeader('Location', getenv('FRONTEND_URL') . '/dashboard');
    }
}
