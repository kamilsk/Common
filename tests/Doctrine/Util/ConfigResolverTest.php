<?php

declare(strict_types = 1);

namespace OctoLab\Common\Doctrine\Util;

use Doctrine\DBAL\Types\Type;
use OctoLab\Common\Config\Loader;
use OctoLab\Common\Doctrine\Type\IntegerMapTypeMock;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class ConfigResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function resolve()
    {
        ConfigResolver::resolve([
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
