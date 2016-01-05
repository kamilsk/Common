<?php

namespace Test\OctoLab\Common\Config;

use OctoLab\Common\Config\JsonConfig;
use OctoLab\Common\Config\Loader\JsonFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * phpunit tests/Config/JsonConfigTest.php
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class JsonConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \DomainException
     */
    public function throwDomainException()
    {
        $config = new JsonConfig(new JsonFileLoader(new FileLocator()));
        $config->load('not_json.file', true);
    }
}
