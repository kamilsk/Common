<?php

namespace OctoLab\Common\Config;

use OctoLab\Common\Util\ArrayHelper;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class SimpleConfig implements \ArrayAccess, \Iterator
{
    /** @var array */
    protected $config;

    /**
     * @param array $config
     *
     * @api
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @deprecated internals
     * @todo will removed since 2.0 version
     *
     * @param array $placeholders
     *
     * @return $this
     *
     * @api
     */
    public function replace(array $placeholders)
    {
        trigger_error(sprintf('%s is deprecated.', __METHOD__), E_USER_DEPRECATED);
        if (isset($this->config['parameters'])) {
            ArrayHelper::transform($this->config['parameters'], $placeholders);
            $placeholders = array_merge($this->config['parameters'], $placeholders);
            unset($this->config['parameters']);
        }
        ArrayHelper::transform($this->config, $placeholders);
        return $this;
    }

    /**
     * @deprecated use {@link ArrayAccess} interface
     * @todo will removed since 2.0 version
     *
     * @return array
     *
     * @api
     */
    public function toArray()
    {
        trigger_error(sprintf('%s is deprecated.', __METHOD__), E_USER_DEPRECATED);
        if (isset($this->config['imports'])) {
            unset($this->config['imports']);
        }
        if (isset($this->config['parameters'])) {
            ArrayHelper::transform($this->config, $this->config['parameters']);
            unset($this->config['parameters']);
        }
        return $this->config;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function offsetExists($offset)
    {
        return (bool) ArrayHelper::findByPath($this->config, $offset);
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
     * @param mixed $offset
     * @param mixed $value
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
     * @param mixed $offset
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
}
