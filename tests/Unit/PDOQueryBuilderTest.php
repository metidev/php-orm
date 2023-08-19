<?php

namespace Tests\Unit;

use App\Database\PDODatabaseConnection;
use App\Database\PDOQueryBuilder;
use App\Exceptions\ConfigNotValidException;
use App\Exceptions\DatabaseConnectionException;
use App\Helpers\Config;
use PHPUnit\Framework\TestCase;

class PDOQueryBuilderTest extends TestCase
{
    private $queryBuilder;

    /**
     * @throws ConfigNotValidException
     * @throws DatabaseConnectionException
     */
    public function setUp(): void
    {
        $pdoConnection = new PDODatabaseConnection($this->getConfig());
        $this->queryBuilder = new PDOQueryBuilder($pdoConnection->connect());
        $this->queryBuilder->beginTransaction();
        parent::setUp();
    }

    public function testItCanCreateData(): void
    {
        $result = $this->insertInToDb();
        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    public function testItCanUpdateData()
    {
        $this->insertInToDb();

        $result = $this->queryBuilder
            ->table('bugs')
            ->where('user', 'meti dev')
            ->update(['email' => 'meti-dev@gmail.com', 'name' => 'mehdicode']);

        $this->assertEquals(1, $result);
        return $result;
    }

    public function testCanUpdateMultipleWhere(): void
    {
        $this->insertInToDb();
        $this->insertInToDb(['user' => 'mmd']);
        $result = $this->queryBuilder
            ->table('bugs')
            ->where('user', 'meti dev')
            ->where('link', 'http://link.com')
            ->update(['name' => 'after multiplication']);

        $this->assertEquals(1, $result);
    }

    public function testItCanDeleteRecord(): void
    {
        $this->insertInToDb();
        $this->insertInToDb();
        $this->insertInToDb();
        $this->insertInToDb();

        $result = $this->queryBuilder
            ->table('bugs')
            ->where('user', 'meti dev')
            ->delete();

        $this->assertEquals(4, $result);
    }

    public function testItCanFetchData(): void
    {
        $this->multipleInsertInToDb(10);
        $this->multipleInsertInToDb(10, ['user' => 'mmd asghari']);

        $result = $this->queryBuilder
            ->table('bugs')
            ->where('user', 'meti dev')
            ->get();

        $this->assertIsArray($result);
        $this->assertCount(10, $result);
    }

    public function testItCanFetchSpecificColumns(): void
    {
        $this->multipleInsertIntoDb(10);
        $this->multipleInsertIntoDb(10, ['name' => 'New']);
        $result = $this->queryBuilder
            ->table('bugs')
            ->where('name', 'New')
            ->get(['name', 'user']);
        $this->assertIsArray($result);
        $this->assertTrue(property_exists($result[0], 'name'));
        $this->assertTrue(property_exists($result[0], 'user'));
        $result = json_decode(json_encode($result[0]), true);
        $this->assertEquals(['name', 'user'], array_keys($result));
    }

    public function testItCanGetFirstRow(): void
    {
        $this->multipleInsertIntoDb(10, ['name' => 'First Row']);
        $result = $this->queryBuilder
            ->table('bugs')
            ->where('name', 'First Row')
            ->first();
        $this->assertIsObject($result);
        $this->assertTrue(property_exists($result, 'id'));
        $this->assertTrue(property_exists($result, 'email'));
        $this->assertTrue(property_exists($result, 'link'));
        $this->assertTrue(property_exists($result, 'name'));
        $this->assertTrue(property_exists($result, 'user'));
    }

    public function testItCanFindWithID(): void
    {
        $this->insertInToDb();
        $id = $this->insertInToDb(['name' => 'For Find']);
        $result = $this->queryBuilder
            ->table('bugs')
            ->find($id);

        $this->assertIsObject($result);
        $this->assertEquals('For Find', $result->name);
    }

    public function testItCanFindBy(): void
    {
        $this->insertInToDb();
        $id = $this->insertInToDb(['name' => 'For Find By']);
        $result = $this->queryBuilder
            ->table('bugs')
            ->findBy('name', 'For Find By');

        $this->assertIsObject($result);
        $this->assertEquals($id, $result->id);
    }

    public function testItReturnsEmptyArrayWhenRecordNotFound(): void
    {
        $result = $this->queryBuilder
            ->table('bugs')
            ->where('user', 'mehti')
            ->get();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testItReturnsNullWhenFirstRecordNotFound(): void
    {
        $this->multipleInsertInToDb(4);
        $result = $this->queryBuilder
            ->table('bugs')
            ->where('user', 'mehti')
            ->first();

        $this->assertNull($result);
        $this->assertEmpty($result);
    }

    public function testItReturnZeroWhenRecordNotFoundForUpdate(): void
    {
        $this->multipleInsertInToDb(4);
        $result = $this->queryBuilder
            ->table('bugs')
            ->where('user', 'mehti')
            ->update(['name' => 'Test']);

        $this->assertEquals(0, $result);
    }

    private function getConfig()
    {
        return Config::get('database', 'pdo_testing');
    }

    private function insertInToDb($options = []): int
    {
        $data = array_merge([
            'name' => 'first bug report',
            'link' => 'http://link.com',
            'user' => 'meti dev',
            'email' => 'mehdicode3@gmail.com'], $options);
        return $this->queryBuilder->table('bugs')->create($data);
    }

    private function multipleInsertInToDb($count, $options = []): void
    {
        for ($i = 1; $i <= $count; $i++) {
            $this->insertInToDb($options);
        }
    }

    public function tearDown(): void
    {
//        $this->queryBuilder->truncateAllTable();
        $this->queryBuilder->rollback();

        parent::tearDown();
    }
}