<?php

declare(strict_types = 1);

namespace OctoLab\Common\Doctrine\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use function OctoLab\Common\camelize;

/**
 * Driver based migrations.
 *
 * Strategy:
 * - migration determines a preparatory method (must be protected or public), e.g. preMysqliUp, preIbmDb2Down, etc.
 * - the preparatory method fills the "queries" property
 * - "up" and "down" run these queries
 * IMPORTANT: caution use inheritance chain to avoid bugs associated with need to override "up" and "down" hooks.
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
abstract class DriverBasedMigration extends AbstractMigration
{
    /** @var string[] */
    protected $queries = [];

    /**
     * @param string $driverName
     *
     * @return string
     *
     * @api
     */
    public static function normalizeDriverName(string $driverName): string
    {
        return camelize($driverName);
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
     * @param Schema $schema
     *
     * @api
     */
    final public function postDown(Schema $schema)
    {
        $this->prepare('post', 'Down', $schema);
    }

    /**
     * @param Schema $schema
     *
     * @api
     */
    final public function postUp(Schema $schema)
    {
        $this->prepare('post', 'Up', $schema);
    }

    /**
     * @param Schema $schema
     *
     * @api
     */
    final public function preDown(Schema $schema)
    {
        $this->prepare('pre', 'Down', $schema);
    }

    /**
     * @param Schema $schema
     *
     * @api
     */
    final public function preUp(Schema $schema)
    {
        $this->prepare('pre', 'Up', $schema);
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
     * @param string $prefix
     * @param string $postfix
     * @param Schema $schema
     */
    private function prepare(string $prefix, string $postfix, Schema $schema)
    {
        $method = $this->resolve($prefix, $postfix);
        if (method_exists($this, $method)) {
            $this->{$method}($schema);
        }
    }

    /**
     * @param string $prefix
     * @param string $postfix
     *
     * @return string
     */
    private function resolve(string $prefix, string $postfix): string
    {
        return $prefix . static::normalizeDriverName($this->connection->getDriver()->getName()) . $postfix;
    }
}
