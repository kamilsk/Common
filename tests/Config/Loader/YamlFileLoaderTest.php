<?php

namespace Test\OctoLab\Common\Config;

use OctoLab\Common\Config\Loader\YamlFileLoader;
use OctoLab\Common\Config\Parser\DipperYamlParser;
use OctoLab\Common\Config\Parser\SymfonyYamlParser;
use Symfony\Component\Config\FileLocator;
use Test\OctoLab\Common\TestCase;

/**
 * phpunit tests/Config/Loader/YamlFileLoaderTest.php
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class YamlFileLoaderTest extends TestCase
{
    /**
     * @return YamlFileLoader[]
     */
    public function loaderProvider()
    {
        return [
            [new YamlFileLoader(new FileLocator(), new SymfonyYamlParser())],
            [new YamlFileLoader(new FileLocator(), new DipperYamlParser())],
        ];
    }

    /**
     * @test
     * @dataProvider loaderProvider
     *
     * @param YamlFileLoader $loader
     */
    public function supported(YamlFileLoader $loader)
    {
        self::assertTrue($loader->supports('/some/path/to/supported.yml'));
    }

    /**
     * @test
     * @dataProvider loaderProvider
     *
     * @param YamlFileLoader $loader
     */
    public function unsupported(YamlFileLoader $loader)
    {
        self::assertFalse($loader->supports('/some/path/to/unsupported.xml'));
    }

    /**
     * @test
     * @dataProvider loaderProvider
     *
     * @param YamlFileLoader $loader
     */
    public function content(YamlFileLoader $loader)
    {
        $loader->load($this->getConfigPath());
        $expected = [
            [
                'imports' => [
                    [
                        'resource' => 'parameters.yml',
                    ],
                    [
                        'resource' => 'component/config.yml',
                    ],
                ],
                'component' => [
                    'parameter' => 'base component\'s parameter will be overwritten by root config',
                    'placeholder_parameter' => '%placeholder%',
                    'constant' => 'const(E_ALL)',
                ],
            ],
            [
                'parameters' => [
                    'parameter' => 'will overwrite parameter',
                ],
            ],
            [
                'imports' => [
                    [
                        'resource' => 'base.yml',
                    ],
                ],
                'component' => [
                    'parameter' => 'base component\'s parameter will be overwritten by component config',
                ],
            ],
            [
                'component' => [
                    'parameter' => 'base parameter will be overwritten',
                    'base_parameter' => 'base parameter will not be overwritten',
                ],
            ],
        ];
        self::assertEquals($expected, $loader->getContent());
    }
    /**
     * @test
     */
    public function load()
    {
        // DipperYamlParser is not compatible
        $loader = new YamlFileLoader(new FileLocator(), new SymfonyYamlParser());
        $loader->load($this->getConfigPath('empty'));
        self::assertEmpty($loader->getContent());
    }

    /**
     * @test
     * @dataProvider loaderProvider
     * @expectedException \InvalidArgumentException
     *
     * @param YamlFileLoader $loader
     */
    public function throwInvalidArgumentExceptionByNotLocalFile(YamlFileLoader $loader)
    {
        $loader->load('http://example.com/');
    }
}
