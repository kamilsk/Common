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
    /** @var string */
    private $defaultName;
    /** @var ComponentFactory */
    private $factory;
    /** @var string[] channel ids cache */
    private $keys;

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
            throw new \InvalidArgumentException();
        }
        $this->factory = $factory;
        $this->defaultName = $defaultName;
        $this->defaultChannel = $config['default_channel'] ?? key($config['channels']);
        foreach ($config['channels'] as $id => $channelConfig) {
            $this->keys[$id] = true;
            if (!isset($channelConfig['arguments'])) {
                $name = $channelConfig['name'] ?? $defaultName;
                $config['channels'][$id]['arguments'] = [$name];
            }
        }
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
        return $this->offsetGet($id);
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
        return $this->offsetGet($this->defaultChannel);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function offsetExists($offset)
    {
        return isset($this->keys[$offset]);
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
    public function valid()
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
     * @param string $id
     *
     * @return mixed
     */
    private function getComponent(string $id)
    {
        //
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
}
