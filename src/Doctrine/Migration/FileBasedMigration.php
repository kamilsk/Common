<?php

declare(strict_types = 1);

namespace OctoLab\Common\Doctrine\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use OctoLab\Common\Doctrine\Util\Parser;

/**
 * File based migrations.
 *
 * Pattern: [/path/to/sql/migrations/][<major version>/[<minor version>/[<patch>/]]][<ticket>/(upgrade|downgrade).sql]
 * - getBasePath()
 * - getMajorVersion()
 * - getMinorVersion()
 * - getPatch()
 * - item of migration list
 * Strategy:
 * - major version extends FileBasedMigration
 * - minor version extends major version
 * - patch extends minor version
 * IMPORTANT: caution use inheritance to avoid bugs associated with need to override "up" and "down" migration list.
 *
 * @see http://semver.org
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
abstract class FileBasedMigration extends AbstractMigration
{
    /** @var string[] */
    protected $queries = [];

    /**
     * @return string
     *
     * @api
     */
    abstract public function getBasePath(): string;

    /**
     * @return string[]
     *
     * @api
     */
    abstract public function getDowngradeMigrations(): array;

    /**
     * @return string
     *
     * @api
     */
    abstract public function getMajorVersion(): string;

    /**
     * @return string[]
     *
     * @api
     */
    abstract public function getUpgradeMigrations(): array;

    /**
     * @param Schema $schema
     *
     * @api
     */
    final public function down(Schema $schema)
    {
        $this->routine();
    }

    /**
     * @param Schema $schema
     *
     * @api
     */
    final public function preDown(Schema $schema)
    {
        $this->prepare(array_map(function (string $migration) : string {
            return $this->getFullPath($migration);
        }, $this->getDowngradeMigrations()));
    }

    /**
     * @param Schema $schema
     *
     * @api
     */
    final public function preUp(Schema $schema)
    {
        $this->prepare(array_map(function (string $value) : string {
            return $this->getFullPath($value);
        }, $this->getUpgradeMigrations()));
    }

    /**
     * @param Schema $schema
     *
     * @api
     */
    final public function up(Schema $schema)
    {
        $this->routine();
    }

    /**
     * @param string $migration
     *
     * @return string
     *
     * @api
     */
    public function getFullPath(string $migration): string
    {
        return implode('/', array_merge(
            [$this->getBasePath()],
            array_filter(
                [$this->getMajorVersion(), $this->getMinorVersion(), $this->getPatch()],
                function (string $value) : bool {
                    return $value === '0' || (bool)$value;
                }
            ),
            [$migration]
        ));
    }

    /**
     * @return string
     *
     * @api
     */
    public function getMinorVersion(): string
    {
        return '';
    }

    /**
     * @return string
     *
     * @api
     */
    public function getPatch(): string
    {
        return '';
    }

    /**
     * @return string[]
     *
     * @api
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

    protected function routine()
    {
        foreach ($this->queries as $sql) {
            $this->addSql($sql);
        }
    }

    /**
     * @param string[] $files
     */
    private function prepare(array $files)
    {
        $this->queries = [];
        foreach ($files as $file) {
            assert('is_readable($file)');
            foreach (Parser::extractSql(file_get_contents($file)) as $sql) {
                $this->queries[] = $sql;
            }
        }
    }
}
