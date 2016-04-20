<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog;

use Monolog\Logger;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 *
 * @quality:class [B]
 */
class LoggerLocator implements \ArrayAccess, \Countable, \Iterator
{
    /** @var array<string, array> */
    private $internal;
    /** @var string */
    private $defaultChannel;
    /** @var string[] */
    private $keys;

    /**
     * @param array<string,array> $config
     * @param string $defaultName
     *
     * @throws \InvalidArgumentException
     *
     * @api
     */
    public function __construct(array $config, string $defaultName = 'app')
    {
        $this->internal = InternalConfig::build();
        $this->resolve($config, $defaultName);
    }

    /**
     * @param string $id
     *
     * @return Logger
     *
     * @throws \OutOfRangeException
     *
     * @api
     */
    public function getChannel($id): Logger
    {
        return $this[$id];
    }

    /**
     * @return Logger
     *
     * @throws \OutOfRangeException
     *
     * @api
     */
    public function getDefaultChannel(): Logger
    {
        return $this[$this->defaultChannel];
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function offsetExists($offset)
    {
        return isset($this->internal['storage'][$this->resolveStorageId('channels', $offset)]);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \OutOfRangeException
     *
     * @api
     */
    public function offsetGet($offset)
    {
        return $this->getComponent($this->resolveStorageId('channels', $offset));
    }

    /**
     * {@inheritdoc}
     *
     * @throws \BadMethodCallException
     *
     * @api
     */
    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('Storage is read-only.');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \BadMethodCallException
     *
     * @api
     */
    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('Storage is read-only.');
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function count()
    {
        return count($this->keys);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \OutOfRangeException
     *
     * @api
     */
    public function current()
    {
        if (false === ($id = current($this->keys))) {
            throw new \OutOfRangeException('Current position of pointer is out of range.');
        }
        return $this->getChannel($id);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function next()
    {
        next($this->keys);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function key()
    {
        return current($this->keys);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function valid()
    {
        return current($this->keys);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function rewind()
    {
        reset($this->keys);
    }

    /**
     * @param array $config
     * <pre>['channels' => [...], 'handlers' => [...], 'processors' => [...], 'formatters' => [...]]</pre>
     * @param string $defaultName is default logger name
     *
     * @throws \InvalidArgumentException
     */
    private function resolve(array $config, string $defaultName)
    {
        $this->defaultChannel = $config['default_channel'] ?? key($config['channels']);
        foreach ($config['channels'] as $id => $channelConfig) {
            $this->keys[] = $id;
            if (!isset($channelConfig['arguments'])) {
                $name = $channelConfig['name'] ?? $defaultName;
                $config['channels'][$id]['arguments'] = [$name];
            }
        }
        $this->store($config);
    }

    /**
     * @param array $config
     *
     * @throws \InvalidArgumentException
     */
    private function store(array $config)
    {
        foreach (array_intersect(array_keys($this->internal['rules']), array_keys($config)) as $key) {
            foreach ($config[$key] as $id => $componentConfig) {
                $this->storeComponentConfig($key, $id, $componentConfig);
                $this->storeComponentDependencies($key, $id, $componentConfig);
            }
        }
    }

    /**
     * @param string $key
     * @param string $id
     * @param array $componentConfig
     *
     * @throws \InvalidArgumentException
     *
     * @quality:method [B]
     */
    private function storeComponentConfig(string $key, string $id, array $componentConfig)
    {
        $config = ['calls' => []];
        $rule = $this->internal['rules'][$key];
        if (isset($componentConfig['type'])) {
            $config['class'] = $this->resolveClassName(
                $rule['namespace'],
                $componentConfig['type'],
                $rule['suffix']
            );
        } elseif (isset($componentConfig['class']) || isset($rule['class'])) {
            $config['class'] = $componentConfig['class'] ?? $rule['class'];
        } else {
            throw new \InvalidArgumentException(
                sprintf(
                    'Component config "%s" does not have "class" or "type" option.',
                    Util\Dumper::dumpToString($componentConfig)
                )
            );
        }
        $config['arguments'] = $componentConfig['arguments'] ?? [];
        $this->internal['storage'][$this->resolveStorageId($key, $id)] = $config;
    }

    /**
     * @param string $namespace
     * @param string $type
     * @param string $suffix
     *
     * @return string
     */
    private function resolveClassName(string $namespace, string $type, string $suffix): string
    {
        if (isset($this->internal['dict'][$type])) {
            $class = $this->internal['dict'][$type];
        } else {
            $class = implode('', explode(' ', ucwords(str_replace('_', ' ', $type))));
        }
        return $namespace . '\\' . $class . $suffix;
    }

    /**
     * @param string $key
     * @param string $id
     *
     * @return string
     */
    private function resolveStorageId(string $key, string $id): string
    {
        return sprintf('%s.%s', rtrim($key, 's'), $id);
    }

    /**
     * @param string $key
     * @param string $id
     * @param array $componentConfig
     */
    private function storeComponentDependencies(string $key, string $id, array $componentConfig)
    {
        foreach ($this->internal['rules'][$key]['dependencies'] as $dependencyKey => $methodName) {
            $componentConfig[$dependencyKey] = (array)($componentConfig[$dependencyKey] ?? []);
            foreach ($componentConfig[$dependencyKey] as $dependencyId) {
                $this->storeDependency(
                    $this->resolveStorageId($key, $id),
                    $this->resolveStorageId($dependencyKey, $dependencyId),
                    $methodName
                );
            }
        }
    }

    /**
     * @param string $componentId
     * @param string $dependencyId
     * @param string $methodName
     */
    private function storeDependency(string $componentId, string $dependencyId, string $methodName)
    {
        $this->internal['storage'][$componentId]['calls'][] = [
            'method' => $methodName,
            'dependency' => $dependencyId,
        ];
    }

    /**
     * @param string $storageId
     *
     * @return mixed
     *
     * @throws \OutOfRangeException
     */
    private function getComponent(string $storageId)
    {
        if (!isset($this->internal['storage'][$storageId])) {
            throw new \OutOfRangeException(sprintf('Component with ID "%s" not found.', $storageId));
        }
        if (is_array($this->internal['storage'][$storageId])) {
            $config = $this->internal['storage'][$storageId];
            $reflection = new \ReflectionClass($config['class']);
            $args = $this->resolveConstructorArguments($reflection, $config['arguments']);
            $component = $reflection->newInstanceArgs($args);
            foreach ($config['calls'] as $callback) {
                $component->{$callback['method']}($this->getComponent($callback['dependency']));
            }
            $this->internal['storage'][$storageId] = $component;
        }
        return $this->internal['storage'][$storageId];
    }

    /**
     * @param \ReflectionClass $reflection
     * @param array $arguments
     *
     * @return array
     *
     * @quality:method [B]
     */
    private function resolveConstructorArguments(\ReflectionClass $reflection, array $arguments): array
    {
        if ($arguments === [] || is_int(key($arguments))) {
            return $arguments;
        } else {
            $params = [];
            foreach ($reflection->getConstructor()->getParameters() as $param) {
                $params[$param->getName()] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
            }
            return array_merge($params, array_intersect_key($arguments, $params));
        }
    }
}
