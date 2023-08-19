<?php

namespace Tests\Unit;

use App\Contracts\DatabaseConnectionInterface;
use App\Database\PDODatabaseConnection;
use App\Exceptions\ConfigFileNotFoundException;
use App\Exceptions\ConfigNotValidException;
use App\Exceptions\DatabaseConnectionException;
use App\Helpers\Config;
use PDO;
use PHPUnit\Framework\TestCase;

class PDODatabaseConnectionTest extends TestCase
{
    /**
     * @throws ConfigFileNotFoundException
     */
    public function testPDODatabaseConnectionImplementationDatabaseConnectionInterface(): void
    {
        $config = $this->getConfig();
        $pdoConnection = new PDODatabaseConnection($config);
        $this->assertInstanceOf(DatabaseConnectionInterface::class, $pdoConnection);
    }

    public function testConnectMethodShouldReturnValidInstance()
    {
        $config = $this->getConfig();
        $pdoConnection = new PDODatabaseConnection($config);
        $pdoHandler = $pdoConnection->connect();
        $this->assertInstanceOf(PDODatabaseConnection::class, $pdoHandler);
        return $pdoHandler;
    }

    /**
     * @depends testConnectMethodShouldReturnValidInstance
     */
    public function testConnectMethodShouldBeConnectionDatabase(): void
    {
        $this->assertInstanceOf(PDO::class, $pdoHandler->getConnection());
    }


    /**
     * @throws ConfigNotValidException
     * @throws ConfigFileNotFoundException
     */
    public function testItThrowsExceptionIfConfigIsInvalid(): void
    {
        $this->expectException(DatabaseConnectionException::class);
        $config = $this->getConfig();
        $config['database'] = 'dadsa';
        $pdoConnection = new PDODatabaseConnection($config);
        $pdoConnection->connect();
    }


    /**
     * @throws ConfigFileNotFoundException
     * @throws DatabaseConnectionException
     */
    public function testRecevedConfigHaveRequiredKey(): void
    {
        $this->expectException(ConfigNotValidException::class);
        $config = $this->getConfig();
        unset($config['db_user']);
        $pdoConnection = new PDODatabaseConnection($config);
        $pdoConnection->connect();
    }

    /**
     * @throws ConfigFileNotFoundException
     */
    private function getConfig()
    {
        return Config::get('database', 'pdo_testing');
    }
}