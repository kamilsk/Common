<?php

declare(strict_types = 1);

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
     */
    public function issue30()
    {
        $loader = new FileLoader(new FileLocator(), new Parser\YamlParser());
        self::assertEquals([], $loader->load($this->getConfigPath('others/empty')));
    }

    /**
     * @test
     * @dataProvider loaderProvider
     *
     * @param FileLoader $loader
     * @param string $extension
     */
    public function loadFailure(FileLoader $loader, string $extension)
    {
        try {
            $loader->load('/unknown.file');
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
            self::assertContains('The file "/unknown.file" does not exist.', $e->getMessage());
        }
        $file = $this->getConfigPath('others/unsupported', 'file');
        try {
            $loader->load($file);
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
            self::assertContains(sprintf('The file "%s" is not supported.', $file), $e->getMessage());
        }
        try {
            $loader->load($this->getConfigPath('others/invalid', $extension));
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
     * @param string $extension
     */
    public function loadSuccess(FileLoader $loader, string $extension)
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
        self::assertEquals($expected, $loader->load($this->getConfigPath('config', $extension)));
    }

    /**
     * @return array
     */
    public function loaderProvider(): array
    {
        return [
            [new FileLoader(new FileLocator(), new Parser\JsonParser()), 'json'],
            [new FileLoader(new FileLocator(), new Parser\YamlParser()), 'yml'],
            [new FileLoader(new FileLocator(), new Parser\IniParser()), 'ini'],
        ];
    }

    /**
     * @test
     * @dataProvider loaderProvider
     *
     * @param FileLoader $loader
     * @param string $extension
     */
    public function supports(FileLoader $loader, string $extension)
    {
        self::assertTrue($loader->supports('config.' . $extension));
    }
}
