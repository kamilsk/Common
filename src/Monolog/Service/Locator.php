<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog\Service;

use Monolog\Logger;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class Locator implements \ArrayAccess, \Countable, \Iterator
{
    /** @var string */
    private $defaultChannel;
    /** @var ComponentFactory */
    private $factory;
    /** @var string[] channel ids cache */
    private $keys;
    /** @var array */
    private $storage;

    /**
     * @param array $config
     * @param ComponentFactory $factory
     * @param string $defaultName
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $config, ComponentFactory $factory, string $defaultName = 'app')
    {
        if (empty($config['channels']) || !is_array($config['channels'])) {
            throw new \InvalidArgumentException('Channels not found.');
        }
        $this->defaultChannel = (string)($config['default_channel'] ?? key($config['channels']));
        $this->factory = $factory;
        $this->storage = [];
        $this
            ->enrich($config['channels'], $defaultName)
            ->store($config)
        ;
    }

    /**
     * @param string $id
     *
     * @return Logger
     *
     * @throws \OutOfRangeException
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     *
     * @api
     */
    public function getChannel(string $id): Logger
    {
        return $this->offsetGet($id);
    }

    /**
     * @return Logger
     *
     * @throws \OutOfRangeException
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     *
     * @api
     */
    public function getDefaultChannel(): Logger
    {
        return $this->offsetGet($this->defaultChannel);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function offsetExists($offset): bool
    {
        return isset($this->keys[$offset]);
    }

    /**
     * {@inheritdoc}
     *
     * @return Logger
     *
     * @throws \OutOfRangeException
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     *
     * @api
     */
    public function offsetGet($offset): Logger
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
    public function count(): int
    {
        return count($this->keys);
    }

    /**
     * {@inheritdoc}
     *
     * @return Logger
     *
     * @throws \OutOfRangeException
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     *
     * @api
     */
    public function current(): Logger
    {
        if (null === ($id = key($this->keys))) {
            throw new \OutOfRangeException('Current position of pointer is out of range.');
        }
        return $this->offsetGet($id);
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
        return key($this->keys);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function valid(): bool
    {
        return (bool)current($this->keys);
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
     * @param array $channelConfigs
     * @param string $defaultName
     *
     * @return Locator
     */
    private function enrich(array &$channelConfigs, string $defaultName): Locator
    {
        foreach ($channelConfigs as $channelId => $channelConfig) {
            $this->keys[$channelId] = true;
            if (!isset($channelConfig['arguments'])) {
                $channelConfigs[$channelId]['arguments'] = [$channelConfig['name'] ?? $defaultName];
            }
        }
        return $this;
    }

    /**
     * @param string $id
     *
     * @return mixed
     *
     * @throws \OutOfRangeException
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    private function getComponent(string $id)
    {
        if (!isset($this->storage[$id])) {
            throw new \OutOfRangeException(sprintf('Component with ID "%s" not found.', $id));
        }
        if (is_array($this->storage[$id])) {
            $component = $this->factory->build($this->storage[$id]);
            $this->resolveComponentDependencies($id, $component);
            $this->storage[$id] = $component;
        }
        return $this->storage[$id];
    }

    /**
     * @param string $id
     * @param mixed $component
     * \Monolog\Logger|\Monolog\Handler\HandlerInterface|\Monolog\Formatter\FormatterInterface|callable
     *
     * @throws \OutOfRangeException
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    private function resolveComponentDependencies(string $id, $component)
    {
        foreach ($this->storage[$id]['calls'] as list($method, $args)) {
            foreach ($args as $i => &$arg) {
                if (strpos($arg, '@') === 0) {
                    $arg = $this->getComponent(substr($arg, 1));
                }
            }
            unset($arg);
            $component->{$method}(...$args);
        }
    }

    /**
     * @param string $key
     * @param string $componentId
     *
     * @return string
     */
    private function resolveStorageId(string $key, string $componentId): string
    {
        return sprintf('%s.%s', rtrim($key, 's'), $componentId);
    }

    /**
     * @param array $config
     *
     * @throws \InvalidArgumentException
     */
    private function store(array $config)
    {
        foreach (array_intersect($this->factory->getAvailableComponentKeys(), array_keys($config)) as $key) {
            foreach ($config[$key] as $componentId => $componentConfig) {
                $id = $this->resolveStorageId($key, $componentId);
                $this->storage[$id] = array_merge(['arguments' => [], 'calls' => [], '_key' => $key], $componentConfig);
                $this->storeComponentDependencies($key, $componentConfig, $id);
            }
        }
    }

    /**
     * @param string $key
     * @param array $componentConfig
     * @param string $id
     *
     * @throws \InvalidArgumentException
     */
    private function storeComponentDependencies(string $key, array $componentConfig, string $id)
    {
        foreach ($this->factory->getDependencies($key) as $dependencyKey => $componentMethod) {
            $componentConfig[$dependencyKey] = (array)($componentConfig[$dependencyKey] ?? []);
            foreach ($componentConfig[$dependencyKey] as $componentId) {
                $this->storage[$id]['calls'][] = [
                    $componentMethod,
                    ['@' . $this->resolveStorageId($dependencyKey, $componentId)],
                ];
            }
        }
    }
}
