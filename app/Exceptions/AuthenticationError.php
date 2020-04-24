<?php

namespace App\Exceptions;

use Exception;

class AuthenticationError extends Exception
{
    public function __construct($message = 'An error occured.', $code = 401, Exception $previous = null)
    {
        $this->message = $message;
        $this->code = $code;
    }
}
