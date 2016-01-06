<?php

namespace OctoLab\Common\Monolog\Util;

use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class ConfigResolver
{
    /** @var Logger[] */
    private $channels = [];
    /** @var HandlerInterface[] */
    private $handlers = [];
    /** @var callable[] */
    private $processors = [];
    /** @var FormatterInterface[] */
    private $formatters = [];
    /** @var array[] */
    private $unnamed;
    /** @var string */
    private $defaultChannel = 'default';

    /**
     * @return Logger[]
     *
     * @api
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * @return Logger|false
     *
     * @api
     */
    public function getDefaultChannel()
    {
        return isset($this->channels[$this->defaultChannel])
            ? $this->channels[$this->defaultChannel]
            : current($this->channels);
    }

    /**
     * @return HandlerInterface[]
     *
     * @api
     */
    public function getHandlers()
    {
        return $this->handlers;
    }

    /**
     * @return callable[]
     *
     * @api
     */
    public function getProcessors()
    {
        return $this->processors;
    }

    /**
     * @return FormatterInterface[]
     *
     * @api
     */
    public function getFormatters()
    {
        return $this->formatters;
    }

    /**
     * @param array $config
     * <pre>[..., 'handlers' => [...], 'processors' => [...]]</pre>
     * <pre>['channels' => [...], 'handlers' => [...], 'processors' => [...], 'formatters' => [...]]</pre>
     * @param string $defaultName Is default logger name
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     * @throws \DomainException
     *
     * @api
     */
    public function resolve(array $config, $defaultName = 'app')
    {
        $this->unnamed = [
            'handlers' => [],
            'processors' => [],
            'formatters' => [],
        ];
        $this->channels = [];
        $this->handlers = [];
        $this->processors = [];
        $this->formatters = [];
        if (isset($config['formatters']) && is_array($config['formatters'])) {
            $this->resolveFormatters($config['formatters']);
        }
        if (isset($config['processors']) && is_array($config['processors'])) {
            $this->resolveProcessors($config['processors']);
        }
        if (isset($config['handlers']) && is_array($config['handlers'])) {
            $this->resolveHandlers($config['handlers']);
        }
        if (isset($config['channels']) && is_array($config['channels'])) {
            if (!empty($config['default_channel'])) {
                $this->defaultChannel = $config['default_channel'];
            }
            $this->resolveChannels($config['channels']);
        } else {
            $name = isset($config['name']) ? $config['name'] : $defaultName;
            $this->resolveChannels([$this->defaultChannel => ['name' => $name]]);
            $channel = $this->getDefaultChannel();
            foreach ($this->handlers + $this->unnamed['handlers'] as $handler) {
                $channel->pushHandler($handler);
            }
            foreach ($this->processors + $this->unnamed['processors'] as $processor) {
                $channel->pushProcessor($processor);
            }
        }
        $this->unnamed = null;
        return $this;
    }

    /**
     * @param array $config
     *
     * @throws \InvalidArgumentException
     */
    private function resolveFormatters(array $config)
    {
        foreach ($config as $key => $component) {
            $id = isset($component['id']) ? $component['id'] : $key;
            $formatter = $this->resolveFormatter($component);
            if (is_string($id)) {
                $this->formatters[$id] = $formatter;
            } else {
                $this->unnamed['formatters'][] = $formatter;
            }
        }
    }

    /**
     * @param array $config
     *
     * @throws \InvalidArgumentException
     */
    private function resolveProcessors(array $config)
    {
        foreach ($config as $key => $component) {
            $id = isset($component['id']) ? $component['id'] : $key;
            $processor = $this->resolveProcessor($component);
            if (is_string($id)) {
                $this->processors[$id] = $processor;
            } else {
                $this->unnamed['processors'][] = $processor;
            }
        }
    }

    /**
     * @param array $config
     *
     * @throws \InvalidArgumentException
     */
    private function resolveHandlers(array $config)
    {
        foreach ($config as $key => $component) {
            $id = isset($component['id']) ? $component['id'] : $key;
            $handler = $this->resolveHandler($component);
            if (is_string($id)) {
                $this->handlers[$id] = $handler;
            } else {
                $this->unnamed['handlers'][] = $handler;
            }
            if (isset($component['formatter']) && is_array($component['formatter'])) {
                $formatter = $this->resolveFormatter($component['formatter']);
                $handler->setFormatter($formatter);
                $this->unnamed['formatters'][] = $formatter;
            }
        }
    }

    /**
     * @param array $config
     *
     * @throws \InvalidArgumentException
     * @throws \DomainException
     */
    private function resolveChannels(array $config)
    {
        foreach ($config as $key => $component) {
            $id = isset($component['id']) ? $component['id'] : $key;
            if (is_string($id)) {
                $channel = new Logger(isset($component['name']) ? $component['name'] : $id);
                $this->channels[$id] = $channel;
                if (isset($component['handlers']) && is_array($component['handlers'])) {
                    $this->pushHandlers($channel, $component['handlers']);
                }
                if (isset($component['processors']) && is_array($component['processors'])) {
                    $this->pushProcessors($channel, $component['processors']);
                }
            } else {
                throw new \InvalidArgumentException('A channel must have an identifier.');
            }
        }
    }

    /**
     * @param array $formatter
     *
     * @return FormatterInterface
     *
     * @throws \InvalidArgumentException
     */
    private function resolveFormatter(array $formatter)
    {
        $class = $this->getClass($formatter, 'Formatter', 'Monolog\Formatter');
        $reflection = new \ReflectionClass($class);
        $arguments = $this->getConstructorArguments($reflection, $formatter);
        return $reflection->newInstanceArgs($arguments);
    }

    /**
     * @param array $processor
     *
     * @return callable
     *
     * @throws \InvalidArgumentException
     */
    private function resolveProcessor(array $processor)
    {
        $class = $this->getClass($processor, 'Processor', 'Monolog\Processor');
        $reflection = new \ReflectionClass($class);
        $arguments = $this->getConstructorArguments($reflection, $processor);
        return $reflection->newInstanceArgs($arguments);
    }

    /**
     * @param array $handler
     *
     * @return HandlerInterface
     *
     * @throws \InvalidArgumentException
     */
    private function resolveHandler(array $handler)
    {
        $class = $this->getClass($handler, 'Handler', 'Monolog\Handler');
        $reflection = new \ReflectionClass($class);
        $arguments = $this->getConstructorArguments($reflection, $handler);
        return $reflection->newInstanceArgs($arguments);
    }

    /**
     * @param array $config
     * @param string $component
     * @param string $namespace
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function getClass(array $config, $component, $namespace)
    {
        if (isset($config['type'])) {
            $class = $this->resolveClass($config['type'], $component, $namespace);
        } elseif (isset($config['class'])) {
            $class = $config['class'];
        } else {
            throw new \InvalidArgumentException(sprintf('%s\'s config requires either a type or class.', $component));
        }
        return $class;
    }

    /**
     * @param \ReflectionClass $reflection
     * @param array $config
     *
     * @return array
     */
    private function getConstructorArguments(\ReflectionClass $reflection, array $config)
    {
        $arguments = [];
        if (isset($config['arguments']) && is_array($config['arguments'])) {
            $arguments = $this->resolveConstructorArguments($reflection, $config['arguments']);
        }
        return $arguments;
    }

    /**
     * @param string $type
     * @param string $component
     * @param string $namespace
     *
     * @return string
     */
    private function resolveClass($type, $component, $namespace)
    {
        $class = implode('', explode(' ', ucwords(str_replace('_', ' ', $type))));
        return $namespace . '\\' . $class . $component;
    }

    /**
     * @param \ReflectionClass $reflection
     * @param array $arguments
     *
     * @return array
     */
    private function resolveConstructorArguments(\ReflectionClass $reflection, array $arguments)
    {
        if (defined('HHVM_VERSION') || (defined('PHP_VERSION') && version_compare(PHP_VERSION, '7.0', '>='))) {
            $key = array_keys($arguments)[0];
        } else {
            $key = key($arguments);
        }
        if (is_int($key)) {
            return $arguments;
        } else {
            $params = [];
            foreach ($reflection->getConstructor()->getParameters() as $param) {
                try {
                    $params[$param->getName()] = $param->getDefaultValue();
                } catch (\Exception $e) {
                    $params[$param->getName()] = null;
                }
            }
            return array_merge($params, array_intersect_key($arguments, $params));
        }
    }

    /**
     * @param Logger $logger
     * @param array $handlers
     *
     * @throws \DomainException
     * @throws \InvalidArgumentException
     */
    private function pushHandlers(Logger $logger, array $handlers)
    {
        foreach ($handlers as $component) {
            if (is_string($component)) {
                if (isset($this->handlers[$component])) {
                    $handler = $this->handlers[$component];
                } else {
                    throw new \DomainException(sprintf('Handler with ID "%s" not found.', $component));
                }
            } elseif (is_array($component)) {
                if (isset($component['id'])) {
                    if (isset($this->handlers[$component['id']])) {
                        $handler = $this->handlers[$component['id']];
                    } else {
                        throw new \DomainException(sprintf('Handler with ID "%s" not found.', $component['id']));
                    }
                } else {
                    $handler = $this->resolveHandler($component);
                }
            } else {
                throw new \InvalidArgumentException('Handler configuration must be an array or a string (ID).');
            }
            if (isset($component['formatter'])) {
                $this->setFormatter($handler, $component['formatter']);
            }
            if (isset($component['processors']) && is_array($component['processors'])) {
                $this->pushProcessors($handler, $component['processors']);
            }
            $logger->pushHandler($handler);
        }
    }

    /**
     * @param Logger|HandlerInterface $target
     * @param array $processors
     *
     * @throws \DomainException
     * @throws \InvalidArgumentException
     */
    private function pushProcessors($target, array $processors)
    {
        foreach ($processors as $component) {
            if (is_string($component)) {
                if (isset($this->processors[$component])) {
                    $processor = $this->processors[$component];
                } else {
                    throw new \DomainException(sprintf('Processor with ID "%s" not found.', $component));
                }
            } elseif (is_array($component)) {
                if (isset($component['id'])) {
                    if (isset($this->processors[$component['id']])) {
                        $processor = $this->processors[$component['id']];
                    } else {
                        throw new \DomainException(sprintf('Processor with ID "%s" not found.', $component['id']));
                    }
                } else {
                    $processor = $this->resolveProcessor($component);
                }
            } else {
                throw new \InvalidArgumentException('Processor configuration must be an array or a string (ID).');
            }
            $target->pushProcessor($processor);
        }
    }

    /**
     * @param HandlerInterface $handler
     * @param array|string $component
     *
     * @throws \DomainException
     * @throws \InvalidArgumentException
     */
    private function setFormatter(HandlerInterface $handler, $component)
    {
        if (is_string($component)) {
            if (isset($this->formatters[$component])) {
                $formatter = $this->formatters[$component];
            } else {
                throw new \DomainException(sprintf('Formatter with ID "%s" not found.', $component));
            }
        } elseif (is_array($component)) {
            if (isset($component['id'])) {
                if (isset($this->formatters[$component['id']])) {
                    $formatter = $this->formatters[$component['id']];
                } else {
                    throw new \DomainException(sprintf('Formatter with ID "%s" not found.', $component['id']));
                }
            } else {
                $formatter = $this->resolveFormatter($component);
            }
        } else {
            throw new \InvalidArgumentException('Formatter configuration must be an array or a string (ID).');
        }
        $handler->setFormatter($formatter);
    }
}
