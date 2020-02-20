<?php
namespace App\Controllers\Web;

use Cradle\View;
use App\Helpers\Email;
use Cradle\Controller;
use PHPMailer\PHPMailer\PHPMailer;
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
}
