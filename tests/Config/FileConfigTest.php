<?php

declare(strict_types = 1);

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
     * @return array
     */
    public function fileConfigProvider(): array
    {
        return [
            [
                new FileConfig(new Loader\FileLoader(new FileLocator(), new Loader\Parser\JsonParser())),
                'json',
                ['app:constant' => E_ALL],
            ],
            [
                new FileConfig(new Loader\FileLoader(new FileLocator(), new Loader\Parser\YamlParser())),
                'yml',
                ['app:constant' => E_ALL],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider fileConfigProvider
     *
     * @param FileConfig $config
     * @param string $extension
     * @param array $expected
     */
    public function load(FileConfig $config, string $extension, array $expected)
    {
        $config->load($this->getConfigPath('config', $extension));
        foreach ($expected as $key => $value) {
            self::assertEquals($value, $config[$key]);
        }
    }
}
