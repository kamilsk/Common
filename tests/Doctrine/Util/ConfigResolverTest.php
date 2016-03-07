<?php

namespace OctoLab\Common\Doctrine\Util;

use Doctrine\DBAL\Types\Type;
use OctoLab\Common\Config\Loader;
use OctoLab\Common\Config\FileConfig;
use OctoLab\Common\TestCase;
use Symfony\Component\Config\FileLocator;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class ConfigResolverTest extends TestCase
{
    /**
     * @test
     */
    public function resolve()
    {
        $config = (new FileConfig(new Loader\FileLoader(new FileLocator(), new Loader\Parser\YamlParser())))
            ->load($this->getConfigPath('doctrine/config'))
        ;
        $resolver = new ConfigResolver();
        $resolver->resolve($config['doctrine:dbal']);
        self::assertTrue(Type::hasType('enum'));
    }
}
