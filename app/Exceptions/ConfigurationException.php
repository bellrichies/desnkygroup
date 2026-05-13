<?php

namespace App\Exceptions;

/**
 * ConfigurationException - Thrown when configuration is invalid
 */
class ConfigurationException extends ApplicationException
{
    public function __construct(string $message = 'Configuration error')
    {
        parent::__construct($message);
    }
}
