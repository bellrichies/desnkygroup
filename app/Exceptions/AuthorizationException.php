<?php

namespace App\Exceptions;

/**
 * AuthorizationException - Thrown when user lacks required permissions
 */
class AuthorizationException extends ApplicationException
{
    public function __construct(string $message = 'You do not have permission to perform this action')
    {
        parent::__construct($message, 403);
    }
}
