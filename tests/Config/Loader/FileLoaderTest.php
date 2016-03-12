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
     */
    public function construct()
    {
        $loader = new FileLoader(new FileLocator(), new Parser\JsonParser());
        self::assertTrue($loader->supports('config.json'));
    }

    /**
     * @test
     * @dataProvider loaderProvider
     *
     * @param FileLoader $loader
     */
    public function loadSuccess(FileLoader $loader)
    {
        $expected = [
            'parameters' => [
                'parameter' => 'will overwrite parameter',
            ],
            'app' => [
                'placeholder_parameter' => '%placeholder%',
                'constant' => 'const(E_ALL)',
            ],
            'component' => [
                'parameter' => 'base component\'s parameter will be overwritten by root config',
                'base_parameter' => 'base parameter will not be overwritten',
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
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
            self::assertTrue(true);
        }
        try {
            $loader->load($this->getConfigPath('others/unsupported', 'xml'));
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
            self::assertTrue(true);
        }
        try {
            switch (true) {
                case $loader->supports($this->getConfigPath('others/invalid', 'json')):
                    $loader->load($this->getConfigPath('others/invalid', 'json'));
                    break;
                case $loader->supports($this->getConfigPath('others/invalid', 'yml')):
                    $loader->load($this->getConfigPath('others/invalid', 'yml'));
                    break;
                default:
                    throw new \RuntimeException(sprintf('Unsupported loader %s.', get_class($loader)));
            }
            self::fail(sprintf('%s exception expected.', \Exception::class));
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
     * @test
     */
    public function issue30()
    {
        $loader = new FileLoader(new FileLocator(), new Parser\YamlParser());
        self::assertEquals([], $loader->load($this->getConfigPath('others/empty')));
    }

    /**
     * @return array[]
     */
    public function loaderProvider()
    {
        return [
            [new FileLoader(new FileLocator(), new Parser\JsonParser()), 'json'],
            [new FileLoader(new FileLocator(), new Parser\YamlParser()), 'yml'],
        ];
    }
}
