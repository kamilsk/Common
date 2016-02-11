<?php

namespace OctoLab\Common\Config;

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
     * @deprecated
     *
     * @param array $placeholders
     *
     * @return $this
     *
     * @api
     */
    public function replace(array $placeholders)
    {
        if (isset($this->config['parameters'])) {
            $this->transform($this->config['parameters'], $placeholders);
            $placeholders = array_merge($this->config['parameters'], $placeholders);
            unset($this->config['parameters']);
        }
        $this->transform($this->config, $placeholders);
        return $this;
    }

    /**
     * @deprecated
     *
     * @return array
     *
     * @api
     */
    public function toArray()
    {
        if (isset($this->config['imports'])) {
            unset($this->config['imports']);
        }
        if (isset($this->config['parameters'])) {
            $this->transform($this->config, $this->config['parameters']);
            unset($this->config['parameters']);
        }
        return $this->config;
    }

    /**
     * @inheritDoc
     *
     * @api
     */
    public function offsetExists($offset)
    {
        return $this->resolvePath($offset);
    }

    /**
     * @inheritDoc
     *
     * @api
     */
    public function offsetGet($offset)
    {
        return $this->resolvePath($offset, true);
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
     * @inheritDoc
     *
     * @api
     */
    public function current()
    {
        return current($this->config);
    }

    /**
     * @inheritDoc
     *
     * @api
     */
    public function next()
    {
        next($this->config);
    }

    /**
     * @inheritDoc
     *
     * @api
     */
    public function key()
    {
        return key($this->config);
    }

    /**
     * @inheritDoc
     *
     * @api
     */
    public function valid()
    {
        return empty($this->config) || key($this->config) !== null;
    }

    /**
     * @inheritDoc
     *
     * @api
     */
    public function rewind()
    {
        reset($this->config);
    }

    /**
     * @param array $base
     *
     * @return array
     */
    protected function merge(array $base)
    {
        $mixtures = array_slice(func_get_args(), 1);
        foreach ($mixtures as $mixture) {
            foreach ($mixture as $key => $value) {
                if (is_array($value) && isset($base[$key]) && is_array($base[$key])) {
                    $base[$key] = $this->merge($base[$key], $value);
                } else {
                    $base[$key] = $value;
                }
            }
        }
        return $base;
    }

    /**
     * @param array $array
     * @param array $placeholders
     */
    protected function transform(array &$array, array $placeholders)
    {
        $wrap = function (&$value) {
            $value = sprintf('/%s/', (string) $value);
        };
        array_walk_recursive($array, function (&$param) use ($wrap, $placeholders) {
            if (strpos($param, 'const(') === 0) {
                if (preg_match('/^const\((.*)\)$/', $param, $matches) && defined($matches[1])) {
                    $param = constant($matches[1]);
                }
            } elseif (preg_match('/^%([^%]+)%$/', $param, $matches)) {
                $placeholder = $matches[1];
                if (isset($placeholders[$placeholder])) {
                    $param = $placeholders[$placeholder];
                }
            } elseif (preg_match_all('/%([^%]+)%/', $param, $matches)) {
                array_walk($matches[0], $wrap);
                $pattern = $matches[0];
                $replacement = array_intersect_key($placeholders, array_flip($matches[1]));
                $param = preg_replace($pattern, $replacement, (string) $param);
            }
        });
    }

    /**
     * @param string $path
     * @param bool $return
     *
     * @return bool|null|mixed
     */
    private function resolvePath($path, $return = false)
    {
        if (mb_strpos($path, ':')) {
            $chain = explode(':', $path);
            $scope = $this->config;
            foreach ($chain as $i => $key) {
                if (!is_array($scope) || !array_key_exists($key, $scope)) {
                    return $return ? null : false;
                }
                $scope = $scope[$key];
            }
            return $return ? $scope : true;
        }
        $exists = array_key_exists($path, $this->config);
        if ($return) {
            return $exists ? $this->config[$path] : null;
        }
        return $exists;
    }
}
