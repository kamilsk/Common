<?php

namespace OctoLab\Common\Config;

use OctoLab\Common\TestCase;
use Symfony\Component\Config\FileLocator;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class YamlFileLoaderTest extends TestCase
{
    /**
     * @return array[]
     */
    public function loaderProvider()
    {
        return [
            [new Loader\YamlFileLoader(new FileLocator(), new Parser\SymfonyYamlParser())],
            [new Loader\YamlFileLoader(new FileLocator(), new Parser\DipperYamlParser())],
        ];
    }

    /**
     * @test
     * @dataProvider loaderProvider
     *
     * @param Loader\YamlFileLoader $loader
     */
    public function getContent(Loader\YamlFileLoader $loader)
    {
        self::assertTrue(is_array($loader->getContent()));
    }

    /**
     * @test
     * @dataProvider loaderProvider
     *
     * @param Loader\YamlFileLoader $loader
     */
    public function load(Loader\YamlFileLoader $loader)
    {
        $loader->load($this->getConfigPath());
        $expected = [
            [
                'imports' => [
                    [
                        'resource' => 'parameters.yml',
                    ],
                    'component/config.yml',
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
     * @dataProvider loaderProvider
     *
     * @param Loader\YamlFileLoader $loader
     */
    public function supports(Loader\YamlFileLoader $loader)
    {
        self::assertTrue($loader->supports('/some/path/to/supported.yml'));
        self::assertFalse($loader->supports('/some/path/to/unsupported.json'));
    }
}
