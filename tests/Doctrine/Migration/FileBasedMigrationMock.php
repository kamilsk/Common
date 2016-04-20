<?php

declare(strict_types = 1);

namespace OctoLab\Common\Doctrine\Migration;

use Doctrine\DBAL\Schema\Schema;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class FileBasedMigrationMock extends FileBasedMigration
{
    /**
     * {@inheritdoc}
     */
    public function getBasePath(): string
    {
        return realpath(__DIR__ . '/migrations');
    }

    /**
     * {@inheritdoc}
     */
    public function getMajorVersion(): string
    {
        return '2';
    }

    /**
     * {@inheritdoc}
     */
    public function getUpgradeMigrations(): array
    {
        return ['ISSUE-29/upgrade.sql'];
    }

    /**
     * {@inheritdoc}
     */
    public function getDowngradeMigrations(): array
    {
        return ['ISSUE-29/downgrade.sql'];
    }

    /**
     * @param string $sql
     * @param array $params
     * @param array $types
     */
    protected function addSql($sql, array $params = [], array $types = [])
    {
    }

    /**
     * @param Schema $schema
     */
    public function postUp(Schema $schema)
    {
        $this->queries = [];
    }

    /**
     * @param Schema $schema
     */
    public function postDown(Schema $schema)
    {
        $this->queries = [];
    }
}
