<?php

namespace OctoLab\Common\Tests\Doctrine\Migration;

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
     * @dataProvider migrationProvider
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
     * @dataProvider migrationProvider
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
    public function migrationProvider()
    {
        return [
            [
                (new \ReflectionClass(Mock\DriverBasedMigration::class))
                    ->newInstanceWithoutConstructor()
                    ->mock(new PDOMySql\Driver())
                ,
                new Schema(),
                'MySQL',
            ],
            [
                (new \ReflectionClass(Mock\DriverBasedMigration::class))
                    ->newInstanceWithoutConstructor()
                    ->mock(new PDOPgSql\Driver())
                ,
                new Schema(),
                'PostgreSQL',
            ],
        ];
    }

    /**
     * @param DriverBasedMigration $migration
     * @param string $dbms
     * @param string $direction
     */
    private function checkQueries(DriverBasedMigration $migration, $dbms, $direction)
    {
        $queries = $migration->getQueries();
        self::assertCount(1, $queries);
        self::assertContains($dbms, $queries[0]);
        self::assertContains($direction, $queries[0]);
    }
}
