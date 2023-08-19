<?php

namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;
use App\Exceptions\ConfigNotValidException;
use App\Exceptions\DatabaseConnectionException;
use PDO;
use PDOException;

class PDODatabaseConnection implements DatabaseConnectionInterface
{
    protected $connection;
    protected $config;
    const REQUERIES_CONFIG_KEY = [
        'driver',
        'host',
        'database',
        'db_user',
        'db_pass',
    ];

    /**
     * @throws ConfigNotValidException
     */
    public function __construct(array $config)
    {
        if (!$this->isConfigValid($config)) {
            throw new ConfigNotValidException();
        }
        $this->config = $config;
    }

    /**
     * @throws DatabaseConnectionException
     */
    public function connect(): PDODatabaseConnection
    {
        $dsn = $this->generateDsn($this->config);
        try {
            $this->connection = new PDO(...$dsn); // add fields array to connection
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            throw new DatabaseConnectionException($e->getMessage());
        }

        return $this;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    private function generateDsn(array $config): array
    {
        $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['database']}";

        return [$dsn, $config['db_user'], $config['db_pass']];
    }

    private function isConfigValid(array $config): bool
    {
        $matches = array_intersect(self::REQUERIES_CONFIG_KEY, array_keys($config));
        return count($matches) === count(self::REQUERIES_CONFIG_KEY);
    }
}