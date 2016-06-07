<?php

declare(strict_types = 1);

namespace OctoLab\Common\Command\Doctrine;

use OctoLab\Common\Doctrine\Migration\FileBasedMigration;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class Version20160320120000 extends FileBasedMigration
{
    /**
     * {@inheritdoc}
     */
    public function getBasePath(): string
    {
        return dirname(__DIR__, 2) . '/fixtures/migrations';
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
}
