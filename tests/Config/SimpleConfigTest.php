<?php

namespace Test\OctoLab\Common\Config;

use OctoLab\Common\Config\SimpleConfig;
use Test\OctoLab\Common\TestCase;

/**
 * phpunit tests/Config/SimpleConfigTest.php
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class SimpleConfigTest extends TestCase
{
    /**
     * @test
     * @dataProvider simpleConfigProvider
     *
     * @param SimpleConfig $config
     */
    public function replace(SimpleConfig $config)
    {
        self::assertNotEmpty($config->replace(['placeholder' => 'placeholder'])->toArray());
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     *
     * @param SimpleConfig $config
     */
    public function toArray(SimpleConfig $config)
    {
        self::assertNotEmpty($config->toArray());
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
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     *
     * @param SimpleConfig $config
     */
    public function pathExists(SimpleConfig $config)
    {
        self::assertArrayHasKey('component:parameter', $config);
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     *
     * @param SimpleConfig $config
     */
    public function offsetGet(SimpleConfig $config)
    {
        self::assertNotEmpty($config['component']);
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     *
     * @param SimpleConfig $config
     */
    public function pathGet(SimpleConfig $config)
    {
        self::assertEquals(E_ALL, $config['component:constant']);
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
        $config->next();
        self::assertArrayHasKey('constant', $config->current());
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     *
     * @param SimpleConfig $config
     */
    public function key(SimpleConfig $config)
    {
        self::assertEquals('parameters', $config->key());
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
        $config->next();
        $config->next();
        self::assertFalse($config->valid());
    }

    /**
     * @test
     * @dataProvider simpleConfigProvider
     *
     * @param SimpleConfig $config
     */
    public function rewind(SimpleConfig $config)
    {
        self::assertEquals('parameters', $config->key());
        $config->next();
        self::assertEquals('component', $config->key());
        $config->rewind();
        self::assertEquals('parameters', $config->key());
    }

    /**
     * @return array[]
     */
    public function simpleConfigProvider()
    {
        return [
            [new SimpleConfig(require $this->getConfigPath('config', 'php'))],
        ];
    }
}
