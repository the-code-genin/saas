<?php
namespace App\Controllers\Web;

use Cradle\View;
use App\Models\User;
use App\Helpers\Email;
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
            $this->setHeader('Location', getenv('FRONTEND_URL') . '/dashboard');
            return;
        }

        // Mark user as active
        $user = $user->first();
        $user->status = 'active';
        $user->save();

        // Send activation email.
        $mail = $this->container->get('view')->clearViews()
            ->addView(new View('email/verified.twig', ['user' => $user]))
            ->compileViews();
        Email::send($this->container->get('mailer'), $user->email, 'SaaS account verified!', $mail);

        // Redirect users to their dashboard on the frontend.
        $this->setHeader('Location', getenv('FRONTEND_URL') . '/dashboard');
    }
}
