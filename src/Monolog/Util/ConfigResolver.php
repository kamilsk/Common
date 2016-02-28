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
    /** @var array<string,array>|null */
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
     * @param array<string,array|string> $config
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
                $this->defaultChannel = (string)$config['default_channel'];
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
                    $this->pushProcessors($component['processors'], [$channel, 'pushProcessor']);
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
        return $this->resolveComponent('Formatter', 'Monolog\Formatter', $formatter);
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
        return $this->resolveComponent('Processor', 'Monolog\Processor', $processor);
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
        return $this->resolveComponent('Handler', 'Monolog\Handler', $handler);
    }

    private function resolveComponent($type, $namespace, array $config)
    {
        $class = $this->getClass($type, $namespace, $config);
        $reflection = new \ReflectionClass($class);
        $arguments = $this->getConstructorArguments($reflection, $config);
        return $reflection->newInstanceArgs($arguments);
    }

    /**
     * @param string $type
     * @param string $namespace
     * @param array $config
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function getClass($type, $namespace, array $config)
    {
        if (isset($config['type'])) {
            $class = $this->resolveClass($type, $config['type'], $namespace);
        } elseif (isset($config['class'])) {
            $class = $config['class'];
        } else {
            throw new \InvalidArgumentException(sprintf('%s\'s config requires either a type or class.', $type));
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
     * @param string $subtype
     * @param string $namespace
     *
     * @return string
     */
    private function resolveClass($type, $subtype, $namespace)
    {
        static $exceptions = [
            'Formatter' => [
                'Monolog\Formatter' => [
                    'chrome_php' => 'Monolog\Formatter\ChromePHPFormatter',
                    'mongo_db' => 'Monolog\Formatter\MongoDBFormatter',
                    'mongodb' => 'Monolog\Formatter\MongoDBFormatter',
                ],
            ],
            'Handler' => [
                'Monolog\Handler' => [
                    'chrome_php' => 'Monolog\Handler\ChromePHPHandler',
                    'couch_db' => 'Monolog\Handler\CouchDBHandler',
                    'couchdb' => 'Monolog\Handler\CouchDBHandler',
                    'doctrine_couch_db' => 'Monolog\Handler\DoctrineCouchDBHandler',
                    'doctrine_couchdb' => 'Monolog\Handler\DoctrineCouchDBHandler',
                    'fire_php' => 'Monolog\Handler\FirePHPHandler',
                    'ifttt' => 'Monolog\Handler\IFTTTHandler',
                    'mongo_db' => 'Monolog\Handler\MongoDBHandler',
                    'mongodb' => 'Monolog\Handler\MongoDBHandler',
                    'php_console' => 'Monolog\Handler\PHPConsoleHandler',
                ],
            ],
        ];
        if (isset($exceptions[$type][$namespace][$subtype])) {
            return $exceptions[$type][$namespace][$subtype];
        }
        $class = implode('', explode(' ', ucwords(str_replace('_', ' ', $subtype))));
        return $namespace . '\\' . $class . $type;
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
        foreach ($handlers as $config) {
            $handler = $this->attach(
                'Handler',
                $this->handlers,
                $config,
                [$logger, 'pushHandler'],
                [$this, 'resolveHandler']
            );
            if (isset($config['formatter'])) {
                $this->setFormatter($handler, $config['formatter']);
            }
            if (isset($config['processors']) && is_array($config['processors'])) {
                $this->pushProcessors($config['processors'], [$handler, 'pushProcessor']);
            }
        }
    }

    /**
     * @param array $processors
     * @param callable $push
     *
     * @throws \DomainException
     * @throws \InvalidArgumentException
     */
    private function pushProcessors(array $processors, callable $push)
    {
        foreach ($processors as $config) {
            $this->attach(
                'Processor',
                $this->processors,
                $config,
                $push,
                [$this, 'resolveProcessor']
            );
        }
    }

    /**
     * @param HandlerInterface $handler
     * @param array|string $config
     *
     * @throws \DomainException
     * @throws \InvalidArgumentException
     */
    private function setFormatter(HandlerInterface $handler, $config)
    {
        $this->attach(
            'Formatter',
            $this->formatters,
            $config,
            [$handler, 'setFormatter'],
            [$this, 'resolveFormatter']
        );
    }

    /**
     * @param string $type
     * @param Logger[]|HandlerInterface[]|callable[]|FormatterInterface[] $source
     * @param array|string $componentConfig
     * @param callable $setter
     * @param callable $resolver
     *
     * @return mixed
     *
     * @throws \DomainException
     * @throws \InvalidArgumentException
     */
    private function attach($type, $source, $componentConfig, callable $setter, callable $resolver)
    {
        if (is_string($componentConfig)) {
            if (isset($source[$componentConfig])) {
                $component = $source[$componentConfig];
            } else {
                throw new \DomainException(sprintf('%s with ID "%s" not found.', $type, $componentConfig));
            }
        } elseif (is_array($componentConfig)) {
            if (isset($componentConfig['id'])) {
                if (isset($source[$componentConfig['id']])) {
                    $component = $source[$componentConfig['id']];
                } else {
                    throw new \DomainException(sprintf('%s with ID "%s" not found.', $type, $componentConfig['id']));
                }
            } else {
                $component = $resolver($componentConfig);
            }
        } else {
            throw new \InvalidArgumentException(sprintf('%s configuration must be an array or a string (ID).', $type));
        }
        $setter($component);
        return $component;
    }
}
