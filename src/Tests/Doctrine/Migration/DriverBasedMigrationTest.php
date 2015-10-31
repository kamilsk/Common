<?php

namespace OctoLab\Common\Tests\Doctrine\Migration;

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
     */
    public function up(DriverBasedMigration $migration, Schema $schema)
    {
        $migration->preUp($schema);
        $migration->up($schema);
        $migration->postUp($schema);
    }

    /**
     * @test
     * @dataProvider migrationProvider
     *
     * @param DriverBasedMigration $migration
     * @param Schema $schema
     */
    public function down(DriverBasedMigration $migration, Schema $schema)
    {
        $migration->preDown($schema);
        $migration->down($schema);
        $migration->postDown($schema);
    }

    /**
     * @return array[]
     */
    public function migrationProvider()
    {
        return [
            [
                (new \ReflectionClass(Mock\MysqlMigration::class))->newInstanceWithoutConstructor()->mock(),
                new Schema(),
            ],
            [
                (new \ReflectionClass(Mock\PgsqlMigration::class))->newInstanceWithoutConstructor()->mock(),
                new Schema(),
            ],
        ];
    }
}
