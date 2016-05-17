<?php

declare(strict_types = 1);

namespace OctoLab\Common\Config;

use OctoLab\Common\TestCase;
use OctoLab\Common\Util\ArrayHelper;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class SimpleConfigTest extends TestCase
{
    /**
     * @test
     */
    public function construct()
    {
        $config = new SimpleConfig(
            [
                'parameters' => ['dir' => __DIR__],
                'app' => [
                    'current_dir' => '%dir%',
                ],
            ]
        );
        self::assertEquals(__DIR__, $config['app:current_dir']);
        $config = new SimpleConfig(
            [
                'app' => [
                    'current_dir' => '%dir%',
                ],
            ],
            ['dir' => __DIR__]
        );
        self::assertEquals(__DIR__, $config['app:current_dir']);
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     *
     * @param SimpleConfig $config
     * @param array $expected
     * @param array $paths
     */
    public function offsetExists(SimpleConfig $config, array $expected, array $paths)
    {
        self::assertArrayHasKey(key($expected), $config);
        foreach ($paths as $path) {
            self::assertArrayHasKey($path, $config);
        }
        self::assertArrayNotHasKey('unknown:path', $config);
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     *
     * @param SimpleConfig $config
     * @param array $expected
     * @param string[] $paths
     */
    public function offsetGet(SimpleConfig $config, array $expected, array $paths)
    {
        foreach ($paths as $path) {
            self::assertEquals(ArrayHelper::findByPath($path, $expected), $config[$path]);
        }
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Configuration is read-only.
     *
     * @param SimpleConfig $config
     */
    public function offsetSet(SimpleConfig $config)
    {
        $config['component'] = ['configuration' => 'new'];
        self::assertEquals(['configuration' => 'new'], $config['component']);
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Configuration is read-only.
     *
     * @param SimpleConfig $config
     */
    public function offsetUnset(SimpleConfig $config)
    {
        unset($config['component']);
        self::assertArrayNotHasKey('component', $config);
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     *
     * @param SimpleConfig $config
     * @param array $expected
     */
    public function countTest(SimpleConfig $config, array $expected)
    {
        self::assertCount(count($expected), $config);
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     *
     * @param SimpleConfig $config
     * @param array $expected
     */
    public function current(SimpleConfig $config, array $expected)
    {
        self::assertEquals(current($expected), $config->current());
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     *
     * @param SimpleConfig $config
     */
    public function next(SimpleConfig $config)
    {
        $current = $config->current();
        $config->next();
        self::assertNotEquals($current, $config->current());
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     *
     * @param SimpleConfig $config
     */
    public function key(SimpleConfig $config)
    {
        self::assertTrue(is_scalar($config->key()));
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     *
     * @param SimpleConfig $config
     */
    public function valid(SimpleConfig $config)
    {
        self::assertTrue($config->valid());
        while ($config->valid()) {
            $config->next();
        }
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     *
     * @param SimpleConfig $config
     */
    public function rewind(SimpleConfig $config)
    {
        $current = $config->key();
        self::assertTrue(is_scalar($current));
        $config->next();
        $next = $config->key();
        self::assertTrue(is_scalar($next));
        self::assertNotEquals($current, $next);
        $config->rewind();
        self::assertEquals($current, $config->key());
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     *
     * @param SimpleConfig $config
     * @param array $expected
     */
    public function jsonSerialize(SimpleConfig $config, array $expected)
    {
        self::assertJsonStringEqualsJsonString(json_encode($expected), json_encode($config));
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     *
     * @param SimpleConfig $config
     * @param array $expected
     * @param string[] $paths
     */
    public function invoke(SimpleConfig $config, array $expected, array $paths)
    {
        foreach ($paths as $path) {
            self::assertEquals(ArrayHelper::findByPath($path, $expected), $config($path));
        }
    }

    /**
     * @return array
     */
    public function simpleConfigProvider(): array
    {
        return [
            [
                new SimpleConfig(require $this->getConfigPath('config', 'php'), ['placeholder' => 'placeholder']),
                [
                    'component' => [
                        'parameter' => 'base component\'s parameter will be overwritten by root config',
                        'base_parameter' => 'base parameter will not be overwritten',
                    ],
                    'app' => [
                        'placeholder_parameter' => 'placeholder',
                        'constant' => E_ALL,
                    ],
                ],
                ['component', 'app:constant'],
            ],
        ];
    }
}
