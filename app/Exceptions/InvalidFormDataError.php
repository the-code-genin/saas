<?php

namespace App\Exceptions;

use Exception;

class InvalidFormDataError extends Exception
{
    public function __construct($message = 'Invalid form data.', $code = 105, Exception $previous = null)
    {
        $this->message = $message;
        $this->code = $code;
    }
}
