<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog\Service;

use function OctoLab\Common\camelize;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class ComponentBuilder
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

    /** @var string|null */
    private $class;
    /** @var string[] */
    private $dependencies = [];
    /** @var string|null */
    private $namespace;
    /** @var string|null */
    private $suffix;

    /**
     * @param string $key
     * @param string $method
     *
     * @return ComponentBuilder
     *
     * @api
     */
    public function addDependency(string $key, string $method): ComponentBuilder
    {
        $this->dependencies[$key] = $method;
        return $this;
    }

    /**
     * @return string[]
     *
     * @api
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * @param string|null $class
     * @param string|null $type
     * @param array $args
     *
     * @return mixed
     * \Monolog\Logger
     * \Monolog\Handler\HandlerInterface
     * \Monolog\Formatter\FormatterInterface
     * callable
     *
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     *
     * @api
     */
    public function make(string $class = null, string $type = null, array $args = [])
    {
        $class = $class ?? $this->class ?? $this->resolveClassName($type);
        \assert('class_exists($class)');
        $reflection = new \ReflectionClass($class);
        if ($args !== [] && !\is_int(key($args))) {
            $args = $this->resolveConstructorArguments($args, $reflection);
        }
        return $reflection->newInstanceArgs($args);
    }

    /**
     * @param string $class
     *
     * @return ComponentBuilder
     *
     * @api
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
     *
     * @api
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
     *
     * @api
     */
    public function setSuffix(string $suffix): ComponentBuilder
    {
        $this->suffix = $suffix;
        return $this;
    }

    /**
     * @param string|null $type
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function resolveClassName(string $type = null): string
    {
        if ($type === null) {
            throw new \InvalidArgumentException('Component type is not provided.');
        }
        $class = self::$dict[$type] ?? camelize($type);
        return $this->namespace . '\\' . $class . $this->suffix;
    }

    /**
     * @param array $args
     * @param \ReflectionClass $reflection
     *
     * @return array
     */
    private function resolveConstructorArguments(array $args, \ReflectionClass $reflection): array
    {
        $params = [];
        foreach ($reflection->getConstructor()->getParameters() as $param) {
            $params[$param->getName()] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
        }
        return array_merge($params, array_intersect_key($args, $params));
    }
}
