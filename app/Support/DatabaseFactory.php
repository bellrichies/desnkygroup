<?php

namespace App\Support;

use App\Config;
use App\Database\Connection;

/**
 * Creates application database connections outside the DI bootstrap.
 */
class DatabaseFactory
{
    public static function make(): Connection
    {
        return new Connection((array) Config::get('database.connections.mysql'));
    }
}
