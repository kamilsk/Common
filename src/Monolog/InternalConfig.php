<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog;

/**
 * @internal
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class InternalConfig
{
    public static function build(): array
    {
        $config = [];
        static::setupDict($config);
        static::setupRules($config);
        static::setupStorage($config);
        return $config;
    }

    /**
     * @param array $config
     */
    protected static function setupDict(array &$config)
    {
        $config['dict'] = [
            'chrome_php' => 'ChromePHP',
            'mongo_db' => 'MongoDB',
            'mongodb' => 'MongoDB',
            'couch_db' => 'CouchDB',
            'couchdb' => 'CouchDB',
            'doctrine_couch_db' => 'DoctrineCouchDB',
            'doctrine_couchdb' => 'DoctrineCouchDB',
            'fire_php' => 'FirePHP',
            'ifttt' => 'IFTTT',
            'php_console' => 'PHPConsole',
        ];
    }

    /**
     * @param array $config
     */
    protected static function setupRules(array &$config)
    {
        static::setupChannelRules($config);
        static::setupHandlerRules($config);
        static::setupFormatterRules($config);
        static::setupProcessorRules($config);
    }

    /**
     * @param array $config
     */
    protected static function setupChannelRules(array &$config)
    {
        $config['rules']['channels'] = [
            'class' => 'Monolog\Logger',
            'dependencies' => [
                'handlers' => 'pushHandler',
                'processors' => 'pushProcessor',
            ],
        ];
    }

    /**
     * @param array $config
     */
    protected static function setupHandlerRules(array &$config)
    {
        $config['rules']['handlers'] = [
            'suffix' => 'Handler',
            'namespace' => 'Monolog\Handler',
            'dependencies' => [
                'processors' => 'pushProcessor',
                'formatter' => 'setFormatter',
            ],
        ];
    }

    /**
     * @param array $config
     */
    protected static function setupFormatterRules(array &$config)
    {
        $config['rules']['formatters'] = [
            'suffix' => 'Formatter',
            'namespace' => 'Monolog\Formatter',
            'dependencies' => [],
        ];
    }

    /**
     * @param array $config
     */
    protected static function setupProcessorRules(array &$config)
    {
        $config['rules']['processors'] = [
            'suffix' => 'Processor',
            'namespace' => 'Monolog\Processor',
            'dependencies' => [],
        ];
    }

    /**
     * @param array $config
     */
    protected static function setupStorage(array &$config)
    {
        $config['storage'] = [];
    }
}
