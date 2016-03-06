<?php

namespace OctoLab\Common\Doctrine\Migration;

use Doctrine\DBAL\Schema\Schema;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class FileBasedMigrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider migrationProvider
     *
     * @param FileBasedMigration $migration
     * @param Schema $schema
     */
    public function up(FileBasedMigration $migration, Schema $schema)
    {
        $migration->preUp($schema);
        self::assertNotEmpty($migration->getQueries());
        $migration->up($schema);
        $migration->postUp($schema);
        self::assertEmpty($migration->getQueries());
    }

    /**
     * @test
     * @dataProvider migrationProvider
     *
     * @param FileBasedMigration $migration
     * @param Schema $schema
     */
    public function down(FileBasedMigration $migration, Schema $schema)
    {
        $migration->preDown($schema);
        self::assertNotEmpty($migration->getQueries());
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
            [(new \ReflectionClass(FileBasedMigrationMock::class))->newInstanceWithoutConstructor(), new Schema()],
        ];
    }
}
