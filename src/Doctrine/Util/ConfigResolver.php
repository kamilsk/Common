<?php

namespace OctoLab\Common\Doctrine\Util;

use Doctrine\DBAL\Types\Type;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class ConfigResolver
{
    /**
     * @param array $config
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @api
     */
    public function resolve(array $config)
    {
        if (!empty($config['types'])) {
            $types = Type::getTypesMap();
            foreach ($config['types'] as $name => $class) {
                if (Type::hasType($name)) {
                    continue;
                }
                Type::addType($name, Type::hasType($class) ? $types[$class] : $class);
            }
        }
    }
}
