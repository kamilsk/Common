<?php

namespace OctoLab\Common\Doctrine\Util;

use Doctrine\DBAL\Types\Type;
use OctoLab\Common\Config\Loader;
use OctoLab\Common\Config\FileConfig;
use OctoLab\Common\Doctrine\Type\IntegerMapTypeMock;
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
        (new ConfigResolver())->resolve([
            'default_connection' => 'mysql',
            'connections' => [
                'mysql' => [
                    'driver' => 'pdo_mysql',
                    'host' => 'localhost',
                    'port' => 3306,
                    'dbname' => 'database',
                    'username' => 'user',
                    'password' => 'pass',
                ],
                'sqlite' => [
                    'driver' => 'pdo_sqlite',
                    'memory' => true,
                    'dbname' => 'database',
                    'username' => 'user',
                    'password' => 'pass',
                ],
            ],
            'types' => [
                'enum' => 'string',
                'integer_map' => IntegerMapTypeMock::class,
            ],
        ]);
        self::assertTrue(Type::hasType('enum'));
        self::assertEquals(Type::getType('string'), Type::getType('enum'));
        self::assertTrue(Type::hasType('integer_map'));
        self::assertInstanceOf(IntegerMapTypeMock::class, Type::getType('integer_map'));
    }
}
