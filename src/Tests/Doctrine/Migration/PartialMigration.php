<?php

namespace OctoLab\Common\Tests\Doctrine\Migration;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class PartialMigration extends TestMigration
{
    /** @var array */
    private $upgrade = ['ISSUE-8/upgrade.sql'];
}
