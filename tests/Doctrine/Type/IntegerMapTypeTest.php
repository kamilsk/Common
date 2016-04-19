<?php

declare(strict_types = 1);

namespace OctoLab\Common\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class IntegerMapTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider typeAndPlatformProvider
     *
     * @param IntegerMapTypeMock $type
     */
    public function getValues(IntegerMapTypeMock $type)
    {
        self::assertNotEmpty($type->getValues());
    }

    /**
     * @test
     * @dataProvider typeAndPlatformProvider
     *
     * @param IntegerMapTypeMock $type
     * @param AbstractPlatform $platform
     */
    public function getSQLDeclaration(IntegerMapTypeMock $type, AbstractPlatform $platform)
    {
        self::assertEquals('SMALLINT', $type->getSQLDeclaration([], $platform));
    }

    /**
     * @test
     * @dataProvider typeAndPlatformProvider
     *
     * @param IntegerMapTypeMock $type
     * @param AbstractPlatform $platform
     */
    public function convertToDatabaseValueSuccess(IntegerMapTypeMock $type, AbstractPlatform $platform)
    {
        self::assertEquals(
            IntegerMapTypeMock::MAPPED_VALUE,
            $type->convertToDatabaseValue(IntegerMapTypeMock::MAPPED_VALUE, $platform)
        );
    }

    /**
     * @test
     * @dataProvider typeAndPlatformProvider
     * @expectedException \InvalidArgumentException
     *
     * @param IntegerMapTypeMock $type
     * @param AbstractPlatform $platform
     */
    public function convertToDatabaseValueFailure(IntegerMapTypeMock $type, AbstractPlatform $platform)
    {
        self::assertEquals(
            -IntegerMapTypeMock::MAPPED_VALUE,
            $type->convertToDatabaseValue(-IntegerMapTypeMock::MAPPED_VALUE, $platform)
        );
    }

    /**
     * @return array[]
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function typeAndPlatformProvider()
    {
        if (!Type::hasType('integer_map')) {
            Type::addType('integer_map', IntegerMapTypeMock::class);
        }
        return [
            [Type::getType('integer_map'), new PostgreSqlPlatform()]
        ];
    }
}
