<?php

namespace OctoLab\Common\Config\Loader;

use OctoLab\Common\TestCase;
use Symfony\Component\Config\FileLocator;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class FileLoaderTest extends TestCase
{
    /**
     * @test
     * @dataProvider loaderProvider
     *
     * @param FileLoader $loader
     */
    public function loadSuccess(FileLoader $loader)
    {
        $expected = [
            'component' => [
                'parameter' => 'base parameter will be overwritten',
                'placeholder_parameter' => '%placeholder%',
                'constant' => 'const(E_ALL)',
                'base_parameter' => 'base parameter will not be overwritten',
            ],
            'parameters' => [
                'parameter' => 'will overwrite parameter',
            ],
        ];
        switch (true) {
            case $loader->supports('config.json'):
                $extension = 'json';
                break;
            case $loader->supports('config.yml'):
                $extension = 'yml';
                break;
            default:
                throw new \RuntimeException(sprintf('Unsupported loader %s.', get_class($loader)));
        }
        self::assertEquals($expected, $loader->load($this->getConfigPath('config', $extension)));
    }

    /**
     * @test
     * @dataProvider loaderProvider
     *
     * @param FileLoader $loader
     */
    public function loadFail(FileLoader $loader)
    {
        try {
            $loader->load('/unknown.file');
            self::assertTrue(false);
        } catch (\InvalidArgumentException $e) {
            self::assertTrue(true);
        }
        try {
            $loader->load($this->getConfigPath('unsupported', 'xml'));
            self::assertTrue(false);
        } catch (\InvalidArgumentException $e) {
            self::assertTrue(true);
        }
        try {
            switch (true) {
                case $loader->supports($this->getConfigPath('invalid', 'json')):
                    $loader->load($this->getConfigPath('invalid', 'json'));
                    break;
                case $loader->supports($this->getConfigPath('invalid', 'yml')):
                    $loader->load($this->getConfigPath('invalid', 'yml'));
                    break;
                default:
                    throw new \RuntimeException(sprintf('Unsupported loader %s.', get_class($loader)));
            }
            self::assertTrue(false);
        } catch (\Exception $e) {
            self::assertTrue(true);
        }
    }

    /**
     * @test
     * @dataProvider loaderProvider
     *
     * @param FileLoader $loader
     */
    public function supports(FileLoader $loader)
    {
        self::assertTrue($loader->supports('config.json') || $loader->supports('config.yml'));
        self::assertFalse($loader->supports('config.json') && $loader->supports('config.yml'));
    }

    /**
     * @return array<array<int, FileLoader>>
     */
    public function loaderProvider()
    {
        return [
            [new FileLoader(new FileLocator(), new Parser\JsonParser())],
            [new FileLoader(new FileLocator(), new Parser\YamlParser())],
        ];
    }
}
