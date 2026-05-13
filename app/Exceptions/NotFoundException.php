<?php

namespace App\Exceptions;

/**
 * NotFoundException - Thrown when resource is not found
 */
class NotFoundException extends ApplicationException
{
    public function __construct(string $message = 'Resource not found')
    {
        parent::__construct($message, 404);
    }
}
