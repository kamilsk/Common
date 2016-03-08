<?php

namespace OctoLab\Common\Doctrine\Util;

use Doctrine\DBAL\Types\Type;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class ConfigResolver
{
    /**
     * @quality [B]
     *
     * @param array $config
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @api
     */
    public static function resolve(array $config)
    {
        if (!empty($config['types'])) {
            foreach ($config['types'] as $name => $class) {
                if (!Type::hasType($name)) {
                    Type::addType($name, Type::hasType($class) ? get_class(Type::getType($class)) : $class);
                }
            }
        }
    }
}
