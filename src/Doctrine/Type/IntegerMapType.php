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
     * @return string[] where the key is what is stored in the database, and the value is a human readable description
     * <pre>[int => string]</pre>
     */
    abstract public function getValues();

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getSmallIntTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
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
