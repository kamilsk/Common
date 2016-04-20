<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog;

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
        $this
            ->setupDict()
            ->setupRules()
            ->setupStorage()
        ;
        $this->resolve($config, $defaultName);
    }

    /**
     * @param string $id
     *
     * @return \Monolog\Logger
     *
     * @throws \OutOfRangeException
     *
     * @api
     */
    public function getChannel($id)
    {
        return $this[$id];
    }

    /**
     * @return \Monolog\Logger
     *
     * @throws \OutOfRangeException
     *
     * @api
     */
    public function getDefaultChannel()
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
     */
    public function count()
    {
        return count($this->keys);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \OutOfRangeException
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
     */
    public function next()
    {
        next($this->keys);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return current($this->keys);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return current($this->keys);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        reset($this->keys);
    }

    /**
     * @internal remove LoggerLocatorTest::keys()
     *
     * @return string[]
     */
    public function keys()
    {
        return $this->keys;
    }

    /**
     * @return $this
     */
    protected function setupDict()
    {
        $this->internal['dict'] = [
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
        return $this;
    }

    /**
     * @return $this
     */
    protected function setupRules()
    {
        $this
            ->setupChannelRules()
            ->setupFormatterRules()
            ->setupHandlerRules()
            ->setupProcessorRules()
        ;
        return $this;
    }

    /**
     * @return $this
     */
    protected function setupChannelRules()
    {
        $this->internal['rules']['channels'] = [
            'class' => 'Monolog\Logger',
            'dependencies' => [
                'handlers' => 'pushHandler',
                'processors' => 'pushProcessor',
            ],
        ];
        return $this;
    }

    /**
     * @return $this
     */
    protected function setupFormatterRules()
    {
        $this->internal['rules']['formatters'] = [
            'suffix' => 'Formatter',
            'namespace' => 'Monolog\Formatter',
        ];
        return $this;
    }

    /**
     * @return $this
     */
    protected function setupHandlerRules()
    {
        $this->internal['rules']['handlers'] = [
            'suffix' => 'Handler',
            'namespace' => 'Monolog\Handler',
            'dependencies' => [
                'processors' => 'pushProcessor',
                'formatter' => 'setFormatter',
            ],
        ];
        return $this;
    }

    /**
     * @return $this
     */
    protected function setupProcessorRules()
    {
        $this->internal['rules']['processors'] = [
            'suffix' => 'Processor',
            'namespace' => 'Monolog\Processor',
        ];
        return $this;
    }

    /**
     * @return $this
     */
    protected function setupStorage()
    {
        $this->internal['storage'] = [];
        return $this;
    }

    /**
     * @param array $config
     * <pre>['channels' => [...], 'handlers' => [...], 'processors' => [...], 'formatters' => [...]]</pre>
     * @param string $defaultName is default logger name
     *
     * @throws \InvalidArgumentException
     *
     * @quality:method [D]
     */
    private function resolve(array $config, $defaultName)
    {
        $this->defaultChannel = $config['default_channel'] ?? key($config['channels']);
        if (!isset($config['channels'][$this->defaultChannel])) {
            throw new \InvalidArgumentException(
                sprintf('Channel with ID "%s" does not exists.', $this->defaultChannel)
            );
        }
        foreach ($config['channels'] as $id => $channelConfig) {
            $this->keys[] = $id;
            if (!isset($channelConfig['arguments'])) {
                $name = isset($channelConfig['name']) ? $channelConfig['name'] : $defaultName;
                $config['channels'][$id]['arguments'] = [$name];
            }
        }
        foreach ($this->internal['rules'] as $key => $_) {
            if (isset($config[$key])) {
                foreach ($config[$key] as $id => $componentConfig) {
                    $this->storeComponentConfig($key, $id, $componentConfig);
                    $this->storeComponentDependencies($key, $id, $componentConfig);
                }
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
    private function storeComponentConfig($key, $id, array $componentConfig)
    {
        $config = [
            'class' => null,
            'arguments' => [],
            'calls' => [],
        ];
        $rule = $this->internal['rules'][$key];
        if (isset($componentConfig['class'])) {
            $config['class'] = $componentConfig['class'];
        } elseif (isset($componentConfig['type'])) {
            $config['class'] = $this->resolveClassName(
                $rule['namespace'],
                $componentConfig['type'],
                $rule['suffix']
            );
        } elseif (isset($rule['class'])) {
            $config['class'] = $rule['class'];
        }
        if (!isset($config['class'])) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Component config "%s" does not have "class" or "type" option.',
                    Util\Dumper::dumpToString($componentConfig)
                )
            );
        }
        if (isset($componentConfig['arguments'])) {
            $config['arguments'] = $componentConfig['arguments'];
        }
        $this->internal['storage'][$this->resolveStorageId($key, $id)] = $config;
    }

    /**
     * @param string $namespace
     * @param string $type
     * @param string $suffix
     *
     * @return string
     */
    private function resolveClassName($namespace, $type, $suffix)
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
    private function resolveStorageId($key, $id)
    {
        return sprintf('%s.%s', rtrim($key, 's'), $id);
    }

    /**
     * @param string $key
     * @param string $id
     * @param array $componentConfig
     *
     * @quality:method [B]
     */
    private function storeComponentDependencies($key, $id, array $componentConfig)
    {
        $rule = $this->internal['rules'][$key];
        if (isset($rule['dependencies'])) {
            foreach ($rule['dependencies'] as $dependencyKey => $methodName) {
                if (isset($componentConfig[$dependencyKey])) {
                    if (is_array($componentConfig[$dependencyKey])) {
                        foreach ($componentConfig[$dependencyKey] as $dependencyId) {
                            $this->storeDependency(
                                $this->resolveStorageId($key, $id),
                                $this->resolveStorageId($dependencyKey, $dependencyId),
                                $methodName
                            );
                        }
                    } else {
                        $this->storeDependency(
                            $this->resolveStorageId($key, $id),
                            $this->resolveStorageId($dependencyKey, $componentConfig[$dependencyKey]),
                            $methodName
                        );
                    }
                }
            }
        }
    }

    /**
     * @param string $componentId
     * @param string $dependencyId
     * @param string $methodName
     */
    private function storeDependency($componentId, $dependencyId, $methodName)
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
    private function getComponent($storageId)
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
     */
    private function resolveConstructorArguments(\ReflectionClass $reflection, array $arguments): array
    {
        if ($arguments === [] or is_int(key($arguments))) {
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
