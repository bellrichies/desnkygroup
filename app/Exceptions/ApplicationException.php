<?php

namespace App\Exceptions;

use Exception;

/**
 * ApplicationException - Base exception for application errors
 */
class ApplicationException extends Exception
{
    /**
     * Constructor
     *
     * @param string $message Error message
     * @param int $code Error code
     * @param Exception|null $previous Previous exception for chaining
     */
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
