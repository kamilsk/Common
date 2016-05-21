<?php

declare(strict_types = 1);

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
     * @param array $expected
     */
    public function down(FileBasedMigration $migration, Schema $schema, array $expected)
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
        return [
            [
                (new \ReflectionClass(FileBasedMigrationMock::class))->newInstanceWithoutConstructor(),
                new Schema(),
                [
                    'up' => ['CREATE TABLE test ( id INT, title VARCHAR(8) NOT NULL, PRIMARY KEY (id) )'],
                    'down' => ['DROP TABLE test CASCADE'],
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider migrationProvider
     *
     * @param FileBasedMigration $migration
     * @param Schema $schema
     * @param array $expected
     */
    public function up(FileBasedMigration $migration, Schema $schema, array $expected)
    {
        $migration->preUp($schema);
        $migration->up($schema);
        $migration->postUp($schema);
        self::assertEquals($expected['up'], $migration->getQueries());
    }
}
