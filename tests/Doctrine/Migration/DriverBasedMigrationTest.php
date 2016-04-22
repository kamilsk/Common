<?php

declare(strict_types = 1);

namespace OctoLab\Common\Doctrine\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\PDOMySql;
use Doctrine\DBAL\Driver\PDOPgSql;
use Doctrine\DBAL\Schema\Schema;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class DriverBasedMigrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider migrationProvider
     *
     * @param DriverBasedMigration $migration
     * @param Schema $schema
     * @param array $expected
     */
    public function up(DriverBasedMigration $migration, Schema $schema, array $expected)
    {
        $migration->preUp($schema);
        $migration->up($schema);
        $migration->postUp($schema);
        self::assertEquals($expected['up'], $migration->getQueries());
    }

    /**
     * @test
     * @dataProvider migrationProvider
     *
     * @param DriverBasedMigration $migration
     * @param Schema $schema
     * @param array $expected
     */
    public function down(DriverBasedMigration $migration, Schema $schema, array $expected)
    {
        $migration->preDown($schema);
        $migration->down($schema);
        $migration->postDown($schema);
        self::assertEquals($expected['down'], $migration->getQueries());
    }

    /**
     * @return array
     */
    public function migrationProvider(): array
    {
        static $reflection, $property;
        if (!$reflection) {
            $reflection = new \ReflectionClass(DriverBasedMigrationMock::class);
            $property = $reflection->getProperty('connection');
            $property->setAccessible(true);
        }
        return array_map(
            function (Driver $driver, array $expected) use ($reflection, $property) : array {
                $migration = $reflection->newInstanceWithoutConstructor();
                $property->setValue($migration, new Connection([], $driver));
                return [$migration, new Schema(), $expected];
            },
            [new PDOMySql\Driver(), new PDOPgSql\Driver()],
            [
                ['up' => ['[MySQL][Up] test migration'], 'down' => ['[MySQL][Down] test migration']],
                ['up' => ['[PostgreSQL][Up] test migration'], 'down' => ['[PostgreSQL][Down] test migration']],
            ]
        );
    }
}
