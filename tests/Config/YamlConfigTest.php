<?php

namespace OctoLab\Common\Config;

use Symfony\Component\Config\FileLocator;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class YamlConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \DomainException
     */
    public function throwDomainException()
    {
        $config = new YamlConfig(new Loader\YamlFileLoader(new FileLocator(), new Parser\SymfonyYamlParser()));
        $config->load('not_yaml.file', true);
    }
}
