<?php

namespace Test\OctoLab\Common\Doctrine\Util;

use Doctrine\DBAL\Types\Type;
use OctoLab\Common\Config\Loader\YamlFileLoader;
use OctoLab\Common\Config\Parser\SymfonyYamlParser;
use OctoLab\Common\Config\YamlConfig;
use OctoLab\Common\Doctrine\Util\ConfigResolver;
use Symfony\Component\Config\FileLocator;
use Test\OctoLab\Common\TestCase;

/**
 * phpunit tests/Doctrine/Util/ConfigResolverTest.php
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
            ->load($this->getConfigPath('doctrine/config'))
            ->toArray()
        ;
        $resolver = new ConfigResolver();
        $resolver->resolve($config['doctrine']['dbal']);
        self::assertTrue(Type::hasType('enum'));
    }
}
