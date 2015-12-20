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
     * @return array[]
     */
    public function simpleConfigProvider()
    {
        return [
            [new SimpleConfig(require $this->getConfigPath('config', 'php'))],
        ];
    }
}
