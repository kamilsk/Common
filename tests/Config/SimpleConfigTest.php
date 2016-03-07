<?php

namespace OctoLab\Common\Config;

use OctoLab\Common\TestCase;

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
     */
    public function offsetExists(SimpleConfig $config)
    {
        self::assertArrayHasKey('component', $config);
        self::assertArrayNotHasKey('unknown', $config);
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     *
     * @param SimpleConfig $config
     */
    public function offsetExistsByPath(SimpleConfig $config)
    {
        self::assertArrayHasKey('component:parameter', $config);
        self::assertArrayNotHasKey('component:unknown', $config);
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     *
     * @param SimpleConfig $config
     */
    public function offsetGet(SimpleConfig $config)
    {
        $expected = [
            'parameter' => 'base component\'s parameter will be overwritten by root config',
            'base_parameter' => 'base parameter will not be overwritten',
        ];
        self::assertEquals($expected, $config['component']);
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     *
     * @param SimpleConfig $config
     */
    public function offsetGetByPath(SimpleConfig $config)
    {
        self::assertEquals(E_ALL, $config['app:constant']);
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     * @expectedException \BadMethodCallException
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
     */
    public function current(SimpleConfig $config)
    {
        self::assertArrayHasKey('parameter', $config->current());
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
        $currentKey = $config->key();
        self::assertTrue(is_scalar($currentKey));
        $config->next();
        $nextKey = $config->key();
        self::assertTrue(is_scalar($nextKey));
        self::assertNotEquals($currentKey, $nextKey);
        $config->rewind();
        self::assertEquals($currentKey, $config->key());
    }

    /**
     * @return array[]
     */
    public function simpleConfigProvider()
    {
        return [
            [new SimpleConfig(require $this->getConfigPath('config', 'php'), ['placeholder' => 'placeholder'])],
        ];
    }
}
