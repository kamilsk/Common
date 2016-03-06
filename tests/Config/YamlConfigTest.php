<?php

namespace OctoLab\Common\Config;

use OctoLab\Common\TestCase;
use Symfony\Component\Config\FileLocator;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class YamlConfigTest extends TestCase
{
    /**
     * @test
     * @dataProvider yamlConfigProvider
     *
     * @param YamlConfig $config
     */
    public function replace(YamlConfig $config)
    {
        self::assertNotEmpty($config->replace(['placeholder' => 'placeholder'])->toArray());
    }

    /**
     * @test
     * @expectedException \DomainException
     */
    public function throwDomainException()
    {
        $config = new YamlConfig(new Loader\FileLoader(new FileLocator(), new Loader\Parser\YamlParser()));
        $config->load('not_yaml.file', true);
    }

    /**
     * @return array[]
     */
    public function yamlConfigProvider()
    {
        return [
            [
                (new YamlConfig(new Loader\FileLoader(new FileLocator(), new Loader\Parser\YamlParser())))
                    ->load($this->getConfigPath())
            ],
        ];
    }
}
