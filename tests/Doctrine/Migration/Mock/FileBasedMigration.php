<?php

namespace Test\OctoLab\Common\Doctrine\Migration\Mock;

use Doctrine\DBAL\Schema\Schema;
use OctoLab\Common\Doctrine\Migration\FileBasedMigration as AbstractMigration;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class FileBasedMigration extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function getBasePath()
    {
        return realpath(__DIR__ . '/migrations');
    }

    /**
     * {@inheritdoc}
     */
    public function getMajorVersion()
    {
        return '7';
    }

    /**
     * {@inheritdoc}
     */
    public function getUpgradeMigrations()
    {
        return ['ISSUE-7/upgrade.sql'];
    }

    /**
     * {@inheritdoc}
     */
    public function getDowngradeMigrations()
    {
        return ['ISSUE-7/downgrade.sql'];
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
