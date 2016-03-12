<?php

namespace OctoLab\Common\Config;

use OctoLab\Common\TestCase;
use Symfony\Component\Config\FileLocator;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class FileConfigTest extends TestCase
{
    /**
     * @test
     */
    public function construct()
    {
        $config = new FileConfig(new Loader\FileLoader(new FileLocator(), new Loader\Parser\JsonParser()));
        self::assertArrayNotHasKey('unknown', $config);
    }

    /**
     * @test
     * @dataProvider fileConfigProvider
     *
     * @param FileConfig $config
     * @param string $extension
     */
    public function loadSuccess(FileConfig $config, $extension)
    {
        $config->load($this->getConfigPath('config', $extension));
        self::assertEquals(E_ALL, $config['app:constant']);
    }

    /**
     * @test
     * @dataProvider fileConfigProvider
     * @expectedException \InvalidArgumentException
     *
     * @param FileConfig $config
     */
    public function loadFailure(FileConfig $config)
    {
        $config->load($this->getConfigPath('config', 'xml'));
    }

    /**
     * @return array[]
     */
    public function fileConfigProvider()
    {
        return [
            [new FileConfig(new Loader\FileLoader(new FileLocator(), new Loader\Parser\JsonParser())), 'json'],
            [new FileConfig(new Loader\FileLoader(new FileLocator(), new Loader\Parser\YamlParser())), 'yml'],
        ];
    }
}
