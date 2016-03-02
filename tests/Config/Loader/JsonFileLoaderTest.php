<?php

namespace OctoLab\Common\Config\Loader;

use OctoLab\Common\TestCase;
use Symfony\Component\Config\FileLocator;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class JsonFileLoaderTest extends TestCase
{
    /**
     * @return array[]
     */
    public function loaderProvider()
    {
        return [
            [new JsonFileLoader(new FileLocator(), 512, 0)],
        ];
    }

    /**
     * @test
     * @dataProvider loaderProvider
     *
     * @param JsonFileLoader $loader
     */
    public function getContent(JsonFileLoader $loader)
    {
        self::assertTrue(is_array($loader->getContent()));
    }

    /**
     * @test
     * @dataProvider loaderProvider
     *
     * @param JsonFileLoader $loader
     */
    public function load(JsonFileLoader $loader)
    {
        $loader->load($this->getConfigPath('config', 'json'));
        $expected = [
            [
                'imports' => [
                    [
                        'resource' => 'parameters.json',
                    ],
                    'component/config.json',
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
                        'resource' => 'base.json',
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
     * @param JsonFileLoader $loader
     */
    public function supports(JsonFileLoader $loader)
    {
        self::assertTrue($loader->supports('/some/path/to/supported.json'));
        self::assertFalse($loader->supports('/some/path/to/unsupported.yml'));
    }
}
