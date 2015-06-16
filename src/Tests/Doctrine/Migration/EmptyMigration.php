<?php

namespace OctoLab\Common\Tests\Doctrine\Migration;

use OctoLab\Common\Doctrine\Migration\FileBasedMigration;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class EmptyMigration extends FileBasedMigration
{
    /**
     * @return string
     */
    public function getBasePath()
    {
        return dirname(dirname(__DIR__)) . '/data/migrations';
    }

    /**
     * @return string
     */
    public function getMajorVersion()
    {
        return '7';
    }
}
