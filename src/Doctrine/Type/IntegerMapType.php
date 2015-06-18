<?php

namespace OctoLab\Common\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
abstract class IntegerMapType extends Type
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'integer_map_type';
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return [];
    }

    /**
     * @param array $fieldDeclaration
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getSmallIntTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!isset($this->getValues()[$value])) {
            throw new \InvalidArgumentException('Invalid type.');
        }
        return $value;
    }
}
