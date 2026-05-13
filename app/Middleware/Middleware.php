<?php

namespace App\Middleware;

/**
 * Middleware - Base middleware class
 *
 * All middleware should extend this class and implement the handle() method.
 */
abstract class Middleware
{
    /**
     * Handle the request
     *
     * Return null to continue to next middleware/controller
     * Return any other value to stop execution
     *
     * @return mixed
     */
    abstract public function handle();
}
