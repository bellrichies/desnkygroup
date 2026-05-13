<?php

namespace App\Exceptions;

/**
 * TokenMismatchException - Thrown when CSRF token validation fails
 */
class TokenMismatchException extends ApplicationException
{
    public function __construct(string $message = 'CSRF token mismatch')
    {
        parent::__construct($message, 419);
    }
}
