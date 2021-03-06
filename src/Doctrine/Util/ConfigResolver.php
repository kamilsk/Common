<?php

declare(strict_types = 1);

namespace OctoLab\Common\Doctrine\Util;

use Doctrine\DBAL\Types\Type;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class ConfigResolver
{
    /**
     * @param array $config
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @api
     */
    public static function resolve(array $config)
    {
        $types = (array)($config['types'] ?? []);
        foreach ($types as $name => $class) {
            if (!Type::hasType($name)) {
                Type::addType($name, Type::hasType($class) ? \get_class(Type::getType($class)) : $class);
            }
        }
    }
}
