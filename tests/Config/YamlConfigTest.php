<?php

namespace Test\OctoLab\Common\Config;

use OctoLab\Common\Config\Loader\YamlFileLoader;
use OctoLab\Common\Config\Parser\SymfonyYamlParser;
use OctoLab\Common\Config\YamlConfig;
use Symfony\Component\Config\FileLocator;

/**
 * phpunit tests/Config/YamlConfigTest.php
 *
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
        $config = new YamlConfig(new YamlFileLoader(new FileLocator(), new SymfonyYamlParser()));
        $config->load('not_yaml.file', true);
    }
}
