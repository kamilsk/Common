<?php

namespace Test\OctoLab\Common\Doctrine\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\PDOMySql;
use Doctrine\DBAL\Driver\PDOPgSql;
use Doctrine\DBAL\Schema\Schema;
use OctoLab\Common\Doctrine\Migration\DriverBasedMigration;

/**
 * phpunit src/Tests/Doctrine/Migration/DriverBasedMigrationTest.php
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class DriverBasedMigrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider migrationDataProvider
     *
     * @param DriverBasedMigration $migration
     * @param Schema $schema
     * @param string $dbms
     */
    public function up(DriverBasedMigration $migration, Schema $schema, $dbms)
    {
        $migration->preUp($schema);
        $this->checkQueries($migration, $dbms, '[Up]');
        $migration->up($schema);
        $migration->postUp($schema);
        self::assertEmpty($migration->getQueries());
    }

    /**
     * @test
     * @dataProvider migrationDataProvider
     *
     * @param DriverBasedMigration $migration
     * @param Schema $schema
     * @param string $dbms
     */
    public function down(DriverBasedMigration $migration, Schema $schema, $dbms)
    {
        $migration->preDown($schema);
        $this->checkQueries($migration, $dbms, '[Down]');
        $migration->down($schema);
        $migration->postDown($schema);
        self::assertEmpty($migration->getQueries());
    }

    /**
     * @return array[]
     */
    public function migrationDataProvider()
    {
        static $reflection, $property;
        if (!$reflection) {
            $reflection = new \ReflectionClass(Mock\DriverBasedMigration::class);
            $property = $reflection->getProperty('connection');
            $property->setAccessible(true);
        }
        return array_map(function (Driver $driver, $dbms) use ($reflection, $property) {
            $migration = $reflection->newInstanceWithoutConstructor();
            $property->setValue($migration, new Connection([], $driver));
            return [$migration, new Schema(), $dbms];
        }, [new PDOMySql\Driver(), new PDOPgSql\Driver()], ['MySQL', 'PostgreSQL']);
    }

    /**
     * @param DriverBasedMigration $migration
     * @param string $dbms
     * @param string $direction
     */
    private function checkQueries(DriverBasedMigration $migration, $dbms, $direction)
    {
        $queries = $migration->getQueries();
        self::assertNotEmpty($queries);
        self::assertContains($dbms, current($queries));
        self::assertContains($direction, current($queries));
    }
}
