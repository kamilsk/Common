<?php

namespace OctoLab\Common\Config;

use OctoLab\Common\TestCase;
use Symfony\Component\Config\FileLocator;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class JsonConfigTest extends TestCase
{
    /**
     * @test
     * @dataProvider jsonConfigProvider
     *
     * @param JsonConfig $config
     */
    public function replace(JsonConfig $config)
    {
        self::assertNotEmpty($config->replace(['placeholder' => 'placeholder'])->toArray());
    }

    /**
     * @test
     * @expectedException \DomainException
     */
    public function throwDomainException()
    {
        $config = new JsonConfig(new Loader\JsonFileLoader(new FileLocator()));
        $config->load('not_json.file', true);
    }

    /**
     * @return array[]
     */
    public function jsonConfigProvider()
    {
        return [
            [
                (new JsonConfig(new Loader\JsonFileLoader(new FileLocator())))
                    ->load($this->getConfigPath('config', 'json'))
            ],
        ];
    }
}
