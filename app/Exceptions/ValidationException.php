<?php

namespace App\Exceptions;

/**
 * ValidationException - Thrown when input validation fails
 */
class ValidationException extends ApplicationException
{
    /**
     * @var array Validation errors
     */
    private array $errors;

    /**
     * Constructor
     *
     * @param string $message Error message
     * @param array $errors Validation errors
     */
    public function __construct(string $message = 'Validation failed', array $errors = [])
    {
        $this->errors = $errors;
        parent::__construct($message);
    }

    /**
     * Get validation errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
