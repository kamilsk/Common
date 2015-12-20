<?php

namespace Test\OctoLab\Common\Monolog\Util;

use OctoLab\Common\Config\Loader\YamlFileLoader;
use OctoLab\Common\Config\Parser\SymfonyYamlParser;
use OctoLab\Common\Config\YamlConfig;
use OctoLab\Common\Monolog\Util\ConfigResolver;
use Test\OctoLab\Common\TestCase;
use Symfony\Component\Config\FileLocator;

/**
 * phpunit tests/Monolog/Util/ConfigResolverTest.php
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class ConfigResolverTest extends TestCase
{
    /**
     * @test
     */
    public function resolve()
    {
        $config = (new YamlConfig(new YamlFileLoader(new FileLocator(), new SymfonyYamlParser())))
            ->load($this->getConfigPath('monolog/config'))
            ->replace(['root_dir' => dirname(__DIR__)])
            ->toArray()
        ;
        $resolver = new ConfigResolver();
        $resolver->resolve($config['monolog']);
        self::assertCount(2, $resolver->getHandlers()->keys());
        self::assertCount(3, $resolver->getProcessors());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function throwInvalidArgumentExceptionByGetClass()
    {
        $resolver = new ConfigResolver();
        $resolver->resolve([
            'handlers' => [
                'stream' => [],
            ],
        ]);
    }
}
