<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog\Service;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class Component
{
    /** @var string[] */
    private static $dict = [
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

    /** @var null|string */
    private $class;
    /** @var null|string */
    private $namespace;
    /** @var null|string */
    private $suffix;
    /** @var array */
    private $dependencies = [];

    /**
     * @param string $class
     *
     * @return Component
     */
    public function setClass(string $class): Component
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @param string $namespace
     *
     * @return Component
     */
    public function setNamespace(string $namespace): Component
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @param string $suffix
     *
     * @return Component
     */
    public function setSuffix(string $suffix): Component
    {
        $this->suffix = $suffix;
        return $this;
    }

    /**
     * @param string $key
     * @param string $method
     *
     * @return Component
     */
    public function addDependency(string $key, string $method): Component
    {
        $this->dependencies[$key] = $method;
        return $this;
    }
}
