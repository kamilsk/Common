<?php

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
    abstract public function getBasePath();

    /**
     * @return string
     *
     * @api
     */
    abstract public function getMajorVersion();

    /**
     * @return string[]
     *
     * @api
     */
    abstract public function getUpgradeMigrations();

    /**
     * @return string[]
     *
     * @api
     */
    abstract public function getDowngradeMigrations();

    /**
     * @return null|string
     */
    public function getMinorVersion()
    {
        return null;
    }

    /**
     * @return null|string
     */
    public function getPatch()
    {
        return null;
    }

    /**
     * @return string[]
     *
     * @api
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * @param Schema $schema
     *
     * @api
     */
    final public function preUp(Schema $schema)
    {
        $this->prepare(array_map([$this, 'getFullPath'], $this->getUpgradeMigrations()));
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
     * @param Schema $schema
     *
     * @api
     */
    final public function preDown(Schema $schema)
    {
        $this->prepare(array_map([$this, 'getFullPath'], $this->getDowngradeMigrations()));
    }

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
     * @param string $migration
     *
     * @return string
     *
     * @api
     */
    public function getFullPath($migration)
    {
        return implode('/', array_merge(
            [$this->getBasePath()],
            array_filter([$this->getMajorVersion(), $this->getMinorVersion(), $this->getPatch()], function ($value) {
                return $value === '0' || (bool)$value;
            }),
            [$migration]
        ));
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
        foreach ($files as $file) {
            $queries = Parser::extractSql(file_get_contents($file));
            foreach ($queries as $sql) {
                $this->queries[] = $sql;
            }
        }
    }
}
