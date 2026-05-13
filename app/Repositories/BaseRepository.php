<?php

namespace App\Repositories;

use App\Database\Connection;

/**
 * Base repository for PDO-backed data access classes.
 */
abstract class BaseRepository
{
    protected Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
}
