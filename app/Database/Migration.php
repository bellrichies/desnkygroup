<?php

namespace App\Database;

use App\Exceptions\DatabaseException;

/**
 * Migration - Base class for database migrations
 *
 * Provides fluent interface for creating and modifying database tables.
 * All migrations should extend this class and implement up() and down() methods.
 */
abstract class Migration
{
    /**
     * @var Connection Database connection
     */
    protected Connection $connection;

    /**
     * @var string Current table being modified
     */
    protected string $table;

    /**
     * @var string Current column being defined
     */
    protected string $currentColumn;

    /**
     * @var list<string> Column definitions
     */
    protected array $columns = [];

    /**
     * Constructor
     *
     * @param Connection $connection Database connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Run the migration
     *
     * @return void
     */
    abstract public function up(): void;

    /**
     * Rollback the migration
     *
     * @return void
     */
    abstract public function down(): void;

    /**
     * Create a new table
     *
     * @param string $table Table name
     * @param callable $callback Callback to define columns
     * @return void
     */
    protected function create(string $table, callable $callback): void
    {
        $this->table = $table;
        $this->columns = [];

        call_user_func($callback, $this);

        $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (\n";
        $sql .= implode(",\n", $this->columns);
        $sql .= "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        // Debug output
        // echo "\n--- SQL for table {$table} ---\n{$sql}\n--- End SQL ---\n\n";

        $this->connection->execute($sql);
    }

    /**
     * Drop a table
     *
     * @param string $table Table name
     * @return void
     */
    protected function dropIfExists(string $table): void
    {
        $sql = "DROP TABLE IF EXISTS `{$table}`;";
        $this->connection->execute($sql);
    }

    /**
     * Add ID primary key column
     *
     * @return self
     */
    protected function id(): self
    {
        $this->columns[] = "`id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }

    /**
     * Add string column
     *
     * @param string $name Column name
     * @param int $length Max length (default: 255)
     * @return self
     */
    protected function string(string $name, int $length = 255): self
    {
        $this->currentColumn = $name;
        $this->columns[] = "`{$name}` VARCHAR({$length})";
        return $this;
    }

    /**
     * Add text column
     *
     * @param string $name Column name
     * @return self
     */
    protected function text(string $name): self
    {
        $this->currentColumn = $name;
        $this->columns[] = "`{$name}` TEXT";
        return $this;
    }

    /**
     * Add integer column
     *
     * @param string $name Column name
     * @return self
     */
    protected function integer(string $name): self
    {
        $this->currentColumn = $name;
        $this->columns[] = "`{$name}` INT";
        return $this;
    }

    /**
     * Add unsigned big integer column
     *
     * @param string $name Column name
     * @return self
     */
    protected function unsignedBigInteger(string $name): self
    {
        $this->currentColumn = $name;
        $this->columns[] = "`{$name}` BIGINT UNSIGNED";
        return $this;
    }

    /**
     * Add decimal column
     *
     * @param string $name Column name
     * @param int $precision Total digits
     * @param int $scale Decimal places
     * @return self
     */
    protected function decimal(string $name, int $precision = 8, int $scale = 2): self
    {
        $this->currentColumn = $name;
        $this->columns[] = "`{$name}` DECIMAL({$precision},{$scale})";
        return $this;
    }

    /**
     * Add boolean column
     *
     * @param string $name Column name
     * @return self
     */
    protected function boolean(string $name): self
    {
        $this->currentColumn = $name;
        $this->columns[] = "`{$name}` BOOLEAN";
        return $this;
    }

    /**
     * Add timestamp column
     *
     * @param string $name Column name
     * @return self
     */
    protected function timestamp(string $name): self
    {
        $this->currentColumn = $name;
        $this->columns[] = "`{$name}` TIMESTAMP";
        return $this;
    }

    /**
     * Add created_at and updated_at timestamps
     *
     * @return self
     */
    protected function timestamps(): self
    {
        $this->columns[] = "`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }

    /**
     * Add soft delete (deleted_at) timestamp
     *
     * @return self
     */
    protected function softDeletes(): self
    {
        $this->columns[] = "`deleted_at` TIMESTAMP NULL DEFAULT NULL";
        return $this;
    }

    /**
     * Add foreign key constraint
     *
     * @param string $column Column name
     * @param string $references Referenced column
     * @param string $on Referenced table
     * @param string $onDelete Cascade behavior (default: 'restrict')
     * @return self
     */
    protected function foreign(string $column, string $references, string $on, string $onDelete = 'restrict'): self
    {
        $constraintName = "{$this->table}_{$column}_fk";
        $this->columns[] = "CONSTRAINT `{$constraintName}` FOREIGN KEY (`{$column}`) "
            . "REFERENCES `{$on}`(`{$references}`) ON DELETE "
            . strtoupper($onDelete);
        return $this;
    }

    /**
     * Add unique index
     *
     * @param string|array|null $columns Column name(s), defaults to current column
     * @return self
     */
    protected function unique($columns = null): self
    {
        if ($columns === null) {
            $columns = $this->currentColumn;
        }
        $columns = is_array($columns) ? $columns : [$columns];
        $columnList = implode('`, `', $columns);
        $this->columns[] = "UNIQUE KEY `unique_" . implode('_', $columns) . "` (`{$columnList}`)";
        return $this;
    }

    /**
     * Add index
     *
     * @param string|array|null $columns Column name(s), defaults to current column
     * @return self
     */
    protected function index($columns = null): self
    {
        if ($columns === null) {
            $columns = $this->currentColumn;
        }
        $columns = is_array($columns) ? $columns : [$columns];
        $columnList = implode('`, `', $columns);
        $this->columns[] = "KEY `key_" . implode('_', $columns) . "` (`{$columnList}`)";
        return $this;
    }

    /**
     * Make column nullable
     *
     * @return self
     */
    protected function nullable(): self
    {
        if (!empty($this->columns)) {
            $last = end($this->columns);
            // Skip if it's a constraint or key definition
            if (
                strpos($last, 'CONSTRAINT') === false &&
                strpos($last, 'KEY') === false &&
                strpos($last, 'FOREIGN') === false
            ) {
                // Skip if already nullable
                if (strpos($last, 'NULL') === false) {
                    array_pop($this->columns);
                    $this->columns[] = $last . ' NULL';
                }
            }
        }
        return $this;
    }

    /**
     * Add default value
     *
     * @param mixed $value Default value
     * @return self
     */
    protected function default($value): self
    {
        if (!empty($this->columns)) {
            $last = array_pop($this->columns);
            // Only add DEFAULT if not already present
            if (strpos($last, 'DEFAULT') === false) {
                $default = is_string($value)
                    ? "'{$value}'"
                    : ($value === true ? 'TRUE' : ($value === false ? 'FALSE' : $value));

                $this->columns[] = $last . ' DEFAULT ' . $default;
            } else {
                // Restore the column if DEFAULT is already there
                $this->columns[] = $last;
            }
        }
        return $this;
    }

    /**
     * Get migration name
     *
     * @return string
     */
    public function getName(): string
    {
        return static::class;
    }
}
