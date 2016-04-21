<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog\Service;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class ComponentBuilder
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
    /** @var string[] */
    private $dependencies = [];

    /**
     * @param string $class
     *
     * @return ComponentBuilder
     */
    public function setClass(string $class): ComponentBuilder
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @param string $namespace
     *
     * @return ComponentBuilder
     */
    public function setNamespace(string $namespace): ComponentBuilder
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @param string $suffix
     *
     * @return ComponentBuilder
     */
    public function setSuffix(string $suffix): ComponentBuilder
    {
        $this->suffix = $suffix;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * @param string $key
     * @param string $method
     *
     * @return ComponentBuilder
     */
    public function addDependency(string $key, string $method): ComponentBuilder
    {
        $this->dependencies[$key] = $method;
        return $this;
    }
}
