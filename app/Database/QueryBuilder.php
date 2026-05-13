<?php

namespace App\Database;

/**
 * Small parameterized SQL query builder for common repository reads.
 */
class QueryBuilder
{
    private Connection $connection;

    private string $table;

    /**
     * @var list<string>
     */
    private array $selects = ['*'];

    /**
     * @var list<string>
     */
    private array $wheres = [];

    /**
     * @var list<mixed>
     */
    private array $bindings = [];

    private ?string $orderBy = null;

    private ?int $limit = null;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Set the table to query.
     *
     * @param string $table Table name.
     * @return self
     */
    public function table(string $table): self
    {
        $this->table = $this->identifier($table);

        return $this;
    }

    /**
     * Set selected columns.
     *
     * @param list<string> $columns Column names.
     * @return self
     */
    public function select(array $columns = ['*']): self
    {
        $this->selects = array_map(
            fn (string $column): string => $column === '*' ? '*' : $this->identifier($column),
            $columns
        );

        return $this;
    }

    /**
     * Add a WHERE condition.
     *
     * @param string $column Column name.
     * @param mixed $value Bound value.
     * @param string $operator SQL comparison operator.
     * @return self
     */
    public function where(string $column, $value, string $operator = '='): self
    {
        $allowed = ['=', '!=', '<>', '>', '>=', '<', '<=', 'LIKE'];
        $operator = strtoupper($operator);

        if (!in_array($operator, $allowed, true)) {
            throw new \InvalidArgumentException("Unsupported SQL operator: {$operator}");
        }

        $this->wheres[] = $this->identifier($column) . " {$operator} ?";
        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Add ORDER BY.
     *
     * @param string $column Column name.
     * @param string $direction ASC or DESC.
     * @return self
     */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $this->orderBy = $this->identifier($column) . ' ' . $direction;

        return $this;
    }

    /**
     * Limit result count.
     *
     * @param int $limit Maximum rows.
     * @return self
     */
    public function limit(int $limit): self
    {
        $this->limit = max(1, $limit);

        return $this;
    }

    /**
     * Execute and return all rows.
     *
     * @return array
     */
    public function get(): array
    {
        return $this->connection->query($this->toSql(), $this->bindings);
    }

    /**
     * Execute and return the first row.
     *
     * @return array|null
     */
    public function first(): ?array
    {
        $this->limit(1);
        $rows = $this->get();

        return $rows[0] ?? null;
    }

    /**
     * Build SQL.
     *
     * @return string
     */
    public function toSql(): string
    {
        if (!isset($this->table)) {
            throw new \LogicException('No table selected for query.');
        }

        $sql = 'SELECT ' . implode(', ', $this->selects) . ' FROM ' . $this->table;

        if ($this->wheres !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }

        if ($this->orderBy !== null) {
            $sql .= ' ORDER BY ' . $this->orderBy;
        }

        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        return $sql;
    }

    private function identifier(string $identifier): string
    {
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $identifier)) {
            throw new \InvalidArgumentException("Invalid SQL identifier: {$identifier}");
        }

        return "`{$identifier}`";
    }
}
