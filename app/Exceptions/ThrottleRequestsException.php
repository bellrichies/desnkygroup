<?php

namespace App\Exceptions;

/**
 * ThrottleRequestsException - Thrown when rate limit exceeded
 */
class ThrottleRequestsException extends ApplicationException
{
    public function __construct(string $message = 'Too many requests. Please try again later.')
    {
        parent::__construct($message, 429);
    }
}
