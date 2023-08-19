<?php

namespace Tests\Functional;

use App\Database\PDODatabaseConnection;
use App\Database\PDOQueryBuilder;
use App\Exceptions\ConfigNotValidException;
use App\Exceptions\DatabaseConnectionException;
use App\Helpers\Config;
use App\Helpers\HttpClient;
use PHPUnit\Framework\TestCase;

class CrudTest extends TestCase
{
    private $httpClient;
    private $queryBuilder;

    /**
     * @throws ConfigNotValidException
     * @throws DatabaseConnectionException
     */
    public function setUp(): void
    {
        $pdoConnection = new PDODatabaseConnection($this->getConfig());
        $this->queryBuilder = new PDOQueryBuilder($pdoConnection->connect());
        $this->httpClient = new HttpClient();
        parent::setUp();
    }

    public function testItCanCreateDataWithApi()
    {
        $data = [
            'json' => [
                'name' => 'API',
                'user' => 'meti',
                'email' => 'meti@example.com',
                'link' => 'http://example.com'
            ]
        ];

        $response = $this->httpClient->post('index.php', $data);
        $this->assertEquals(200, $response->getStatusCode());

        $bug = $this->queryBuilder
            ->table('bugs')
            ->where('name', 'API')
            ->where('user', 'meti')
            ->first();

        $this->assertNotNull($bug);
    }

    public function tearDown(): void
    {
        $this->httpClient = null;
        parent::tearDown();
    }

    private function getConfig()
    {
        return Config::get('database', 'pdo_testing');
    }
}