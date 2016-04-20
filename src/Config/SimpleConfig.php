<?php

declare(strict_types = 1);

namespace OctoLab\Common\Config;

use OctoLab\Common\Util\ArrayHelper;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class SimpleConfig implements \ArrayAccess, \Countable, \Iterator, \JsonSerializable
{
    /** @var array */
    protected $config;

    /**
     * @param array $config
     * @param array $placeholders
     *
     * @api
     */
    public function __construct(array $config = [], array $placeholders = [])
    {
        $this->config = $config;
        if (!empty($config) && (!empty($config['parameters']) || !empty($placeholders))) {
            $this->transform($placeholders);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function offsetExists($offset)
    {
        return (bool)ArrayHelper::findByPath($this->config, $offset);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function offsetGet($offset)
    {
        return ArrayHelper::findByPath($this->config, $offset);
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
        throw new \BadMethodCallException('Configuration is read-only.');
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
        throw new \BadMethodCallException('Configuration is read-only.');
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function count()
    {
        return count($this->config);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function current()
    {
        return current($this->config);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function next()
    {
        next($this->config);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function key()
    {
        return key($this->config);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function valid()
    {
        return empty($this->config) || key($this->config) !== null;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function rewind()
    {
        reset($this->config);
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     *
     * @api
     */
    public function jsonSerialize(): array
    {
        return $this->config;
    }

    /**
     * @param array $placeholders
     *
     * @return $this
     */
    protected function transform(array $placeholders)
    {
        if (isset($this->config['parameters'])) {
            ArrayHelper::transform($this->config['parameters'], $placeholders);
            $placeholders = array_merge($this->config['parameters'], $placeholders);
            unset($this->config['parameters']);
        }
        ArrayHelper::transform($this->config, $placeholders);
        $this->rewind();
        return $this;
    }
}
