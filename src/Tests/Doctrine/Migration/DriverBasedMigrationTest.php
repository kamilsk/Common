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
        self::assertCount(1, $migration->getQueries());
        self::assertContains($dbms, $migration->getQueries()[0]);
        self::assertContains('[Up]', $migration->getQueries()[0]);
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
        self::assertCount(1, $migration->getQueries());
        self::assertContains($dbms, $migration->getQueries()[0]);
        self::assertContains('[Down]', $migration->getQueries()[0]);
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
}
