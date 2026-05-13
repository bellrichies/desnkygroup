<?php

namespace App\Database;

use PDO;
use PDOException;
use PDOStatement;
use App\Exceptions\DatabaseException;

/**
 * Connection - MySQL database connection manager
 *
 * Handles PDO connections with singleton pattern, error handling, and transaction support.
 * All queries use prepared statements to prevent SQL injection.
 */
class Connection
{
    /**
     * @var PDO|null PDO connection instance
     */
    private ?PDO $pdo = null;

    /**
     * @var array Database configuration
     */
    private array $config;

    /**
     * Constructor
     *
     * @param array $config Database configuration array
     *   - host: Database host
     *   - port: Database port (default: 3306)
     *   - database: Database name
     *   - username: Database user
     *   - password: Database password
     *   - charset: Character set (default: utf8mb4)
     * @throws DatabaseException If connection fails
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->connect();
    }

    /**
     * Establish database connection
     *
     * @return void
     * @throws DatabaseException If connection fails
     */
    private function connect(): void
    {
        try {
            $host = $this->config['host'] ?? 'localhost';
            $port = $this->config['port'] ?? 3306;
            $database = $this->config['database'];
            $username = $this->config['username'];
            $password = $this->config['password'];
            $charset = $this->config['charset'] ?? 'utf8mb4';

            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";

            $this->pdo = new PDO(
                $dsn,
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            throw new DatabaseException('Database connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Get PDO connection instance
     *
     * @return PDO
     */
    public function getPdo(): PDO
    {
        if ($this->pdo === null) {
            $this->connect();
        }
        return $this->pdo;
    }

    /**
     * Prepare a SQL statement
     *
     * @param string $sql SQL query
     * @return PDOStatement Prepared statement
     * @throws DatabaseException
     */
    public function prepare(string $sql): PDOStatement
    {
        try {
            return $this->getPdo()->prepare($sql);
        } catch (PDOException $e) {
            throw new DatabaseException('Failed to prepare statement: ' . $e->getMessage());
        }
    }

    /**
     * Execute query and return results
     *
     * @param string $sql SQL query
     * @param array $params Query parameters for prepared statement
     * @return array Result rows
     * @throws DatabaseException
     */
    public function query(string $sql, array $params = []): array
    {
        try {
            $statement = $this->prepare($sql);
            $statement->execute($params);
            return $statement->fetchAll();
        } catch (PDOException $e) {
            throw new DatabaseException('Query execution failed: ' . $e->getMessage());
        }
    }

    /**
     * Insert record and return last inserted ID
     *
     * @param string $sql INSERT query
     * @param array $params Query parameters
     * @return int Last inserted ID
     * @throws DatabaseException
     */
    public function insert(string $sql, array $params = []): int
    {
        try {
            $statement = $this->prepare($sql);
            $statement->execute($params);
            return (int) $this->getPdo()->lastInsertId();
        } catch (PDOException $e) {
            throw new DatabaseException('Insert failed: ' . $e->getMessage());
        }
    }

    /**
     * Update records and return affected row count
     *
     * @param string $sql UPDATE query
     * @param array $params Query parameters
     * @return int Number of affected rows
     * @throws DatabaseException
     */
    public function update(string $sql, array $params = []): int
    {
        try {
            $statement = $this->prepare($sql);
            $statement->execute($params);
            return $statement->rowCount();
        } catch (PDOException $e) {
            throw new DatabaseException('Update failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete records and return affected row count
     *
     * @param string $sql DELETE query
     * @param array $params Query parameters
     * @return int Number of affected rows
     * @throws DatabaseException
     */
    public function delete(string $sql, array $params = []): int
    {
        try {
            $statement = $this->prepare($sql);
            $statement->execute($params);
            return $statement->rowCount();
        } catch (PDOException $e) {
            throw new DatabaseException('Delete failed: ' . $e->getMessage());
        }
    }

    /**
     * Execute raw query without fetching results
     *
     * @param string $sql SQL query
     * @param array $params Query parameters
     * @return bool Success
     * @throws DatabaseException
     */
    public function execute(string $sql, array $params = []): bool
    {
        try {
            $statement = $this->prepare($sql);
            return $statement->execute($params);
        } catch (PDOException $e) {
            throw new DatabaseException('Execution failed: ' . $e->getMessage());
        }
    }

    /**
     * Fetch single row
     *
     * @param string $sql SQL query
     * @param array $params Query parameters
     * @return array|null Row data or null
     * @throws DatabaseException
     */
    public function queryOne(string $sql, array $params = []): ?array
    {
        try {
            $statement = $this->prepare($sql);
            $statement->execute($params);
            $result = $statement->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            throw new DatabaseException('Query failed: ' . $e->getMessage());
        }
    }

    /**
     * Begin database transaction
     *
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->getPdo()->beginTransaction();
    }

    /**
     * Commit database transaction
     *
     * @return bool
     */
    public function commit(): bool
    {
        return $this->getPdo()->commit();
    }

    /**
     * Rollback database transaction
     *
     * @return bool
     */
    public function rollBack(): bool
    {
        return $this->getPdo()->rollBack();
    }

    /**
     * Check if transaction is active
     *
     * @return bool
     */
    public function inTransaction(): bool
    {
        return $this->getPdo()->inTransaction();
    }

    /**
     * Disconnect from database
     *
     * @return void
     */
    public function disconnect(): void
    {
        $this->pdo = null;
    }
}
