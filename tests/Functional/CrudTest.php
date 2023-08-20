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
        return $bug;
    }

    /**
     * @depends testItCanCreateDataWithApi
     */
    public function testItCanUpdateDataWitApi($bug)
    {
        $data = [
            'json' => [
                'id' => $bug->id,
                'name' => 'API update'
            ]
        ];

        $response = $this->httpClient->put('index.php', $data);
        $this->assertEquals(200, $response->getStatusCode());
        $bug = $this->queryBuilder
            ->table('bugs')
            ->find($bug->id);
        $this->assertNotNull($bug);
        $this->assertEquals('API update', $bug->name);

    }

    /**
     * @depends testItCanCreateDataWithApi
     */
    public function testItCanFetchDataWithApi($bug)
    {
        $response = $this->httpClient->get('index.php',
            [
                'json' => [
                    'id' => $bug->id
                ]
            ]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('id', json_decode($response->getBody(), true));

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