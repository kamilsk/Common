<?php

namespace OctoLab\Common\Config;

use Symfony\Component\Config\FileLocator;

/**
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
        $config = new JsonConfig(new Loader\JsonFileLoader(new FileLocator()));
        $config->load('not_json.file', true);
    }
}
