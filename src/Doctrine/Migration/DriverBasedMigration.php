<?php

namespace OctoLab\Common\Doctrine\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

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
    private function prepare($prefix, $postfix, Schema $schema)
    {
        $method = $this->resolve($prefix, $postfix);
        if (method_exists($this, $method)) {
            $this->run([$this, $method], $schema);
        }
    }

    /**
     * @param string $prefix
     * @param string $postfix
     *
     * @return string
     */
    private function resolve($prefix, $postfix)
    {
        $driver = $this->connection->getDriver()->getName();
        $parts = explode(' ', ucwords(str_replace('_', ' ', $driver)));
        return $prefix . implode('', $parts) . $postfix;
    }

    /**
     * @param callable $callback
     * @param Schema $schema
     */
    private function run(callable $callback, Schema $schema)
    {
        $callback($schema);
    }
}
