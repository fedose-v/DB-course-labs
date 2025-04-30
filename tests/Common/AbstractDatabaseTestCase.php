<?php
declare(strict_types=1);

namespace App\Tests\Common;

use App\Database\ConnectionProvider;
use PHPUnit\Framework\TestCase;

abstract class AbstractDatabaseTestCase extends TestCase
{
    private \PDO $connection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection = ConnectionProvider::getConnection();
        $this->connection->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->connection->rollback();
        parent::tearDown();
    }

    final protected function getConnection(): \PDO
    {
        return $this->connection;
    }
}