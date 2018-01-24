<?php

declare(strict_types = 1);

namespace OctoLab\Common\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
abstract class IntegerMapType extends Type
{
    /**
     * @return array<int,string> where the key is what is stored in the database,
     * and the value is a human readable description
     *
     * @api
     */
    abstract public function getValues(): array;

    /**
     * {@inheritdoc}
     *
     * @return int
     *
     * @throws \InvalidArgumentException
     *
     * @api
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): int
    {
        \assert('is_int($value)');
        if (!isset($this->getValues()[$value])) {
            throw new \InvalidArgumentException('Invalid type.');
        }
        return $value;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getSmallIntTypeDeclarationSQL($fieldDeclaration);
    }
}
