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
     * @dataProvider fileConfigProvider
     *
     * @param FileConfig $config
     * @param string $extension
     */
    public function loadSuccess(FileConfig $config, $extension)
    {
        $config->load($this->getConfigPath('config', $extension), ['placeholder' => 'placeholder']);
        self::assertEquals(E_ALL, $config['app:constant']);
    }

    /**
     * @test
     * @dataProvider fileConfigProvider
     * @expectedException \InvalidArgumentException
     *
     * @param FileConfig $config
     */
    public function loadFail(FileConfig $config)
    {
        $config->load($this->getConfigPath('config', 'xml'), ['placeholder' => 'placeholder']);
    }

    /**
     * @return array<int,FileConfig[]>
     */
    public function fileConfigProvider()
    {
        return [
            [new FileConfig(new Loader\FileLoader(new FileLocator(), new Loader\Parser\JsonParser())), 'json'],
            [new FileConfig(new Loader\FileLoader(new FileLocator(), new Loader\Parser\YamlParser())), 'yml'],
        ];
    }
}
