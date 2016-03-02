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
    public function pathNotExists(SimpleConfig $config)
    {
        self::assertArrayNotHasKey('unknown:path', $config);
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
            [new SimpleConfig(require $this->getConfigPath('config', 'php'))],
        ];
    }
}
