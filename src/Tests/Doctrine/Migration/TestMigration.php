<?php

namespace OctoLab\Common\Tests\Doctrine\Migration;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class TestMigration extends EmptyMigration
{
    /** @var array */
    private $upgrade = ['ISSUE-7/upgrade.sql'];
    /** @var array */
    private $downgrade = ['ISSUE-7/downgrade.sql'];
}
