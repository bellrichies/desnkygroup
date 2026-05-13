<?php

namespace App\Exceptions;

/**
 * DatabaseException - Thrown when database operations fail
 */
class DatabaseException extends ApplicationException
{
    public function __construct(string $message = 'Database error occurred')
    {
        parent::__construct($message);
    }
}
