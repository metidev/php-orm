<?php

namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;
use PDO;

class PDOQueryBuilder
{
    protected $table;
    protected $connection;
    protected $conditions;
    protected $values;
    protected $statement;

    public function __construct(DatabaseConnectionInterface $connection)
    {
        $this->connection = $connection->getConnection();
    }

    public function table(string $table)
    {
        $this->table = $table;
        return $this;
    }

    public function create(array $data): int
    {
        $placeholder = [];
        foreach ($data as $column => $value) {
            $placeholder[] = '?';
        }
        $feilds = implode(',', array_keys($data));
        $placeholder = implode(',', $placeholder);
        $this->values = array_values($data);
        $sql = "INSERT INTO {$this->table}({$feilds}) VALUES({$placeholder})";
        $this->execute($sql);
        return (int)$this->connection->lastInsertId();
    }

    public function where(string $column, string $value)
    {
        if (is_null($this->conditions)) {
            $this->conditions = "{$column}=?";
        } else {
            $this->conditions .= " AND {$column}=?";
        }
        $this->values[] = $value;
        return $this;
    }

    public function update(array $data)
    {
        $feilds = [];
        foreach ($data as $column => $value) {
            $feilds[] = "{$column}='$value'";
        }
        $feilds = implode(',', $feilds);
        $sql = "UPDATE {$this->table} SET {$feilds} WHERE {$this->conditions}";

        $this->execute($sql);
        return $this->statement->rowCount();
    }

    public function delete()
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->conditions}";
        $this->execute($sql);
        return $this->statement->rowCount();
    }

    public function get(array $columns = ['*'])
    {
        $columns = implode(',', $columns);
        $sql = "SELECT {$columns} FROM {$this->table} WHERE {$this->conditions}";
        $this->execute($sql);
        return $this->statement->fetchAll();
    }

    public function first(array $columns = ['*'])
    {
        $data = $this->get($columns);

        return empty($data) ? null : $data[0];
    }

    public function find(int $id)
    {
        return $this->where('id', $id)->first();
    }

    public function findBy(string $column, $value)
    {
        return $this->where($column, $value)->first();
    }

    public function truncateAllTable(): void
    {
        $query = $this->connection->prepare("SHOW TABLES");
        $query->execute();
        foreach ($query->fetchAll(PDO::FETCH_COLUMN) as $table) {
            $this->connection->prepare("TRUNCATE TABLE `{$table}`")->execute();
        }
    }

    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    public function rollback(): void
    {
        $this->connection->rollBack();
    }

    private function execute(string $sql)
    {
        $this->statement = $this->connection->prepare($sql);
        $this->statement->execute($this->values);
        $this->values = [];
        return $this;
    }
}