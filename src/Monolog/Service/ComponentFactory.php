<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog\Service;

use OctoLab\Common\Util\ArrayHelper;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class ComponentFactory
{
    /**
     * @return ComponentFactory
     */
    public static function withDefaults(): ComponentFactory
    {
        return new self(
            (new ComponentBuilder())
                ->setClass('Monolog\Logger')
                ->addDependency('handlers', 'pushHandler')
                ->addDependency('processors', 'pushProcessor'),
            (new ComponentBuilder())
                ->setNamespace('Monolog\Handler')
                ->setSuffix('Handler')
                ->addDependency('processors', 'pushProcessor')
                ->addDependency('formatter', 'setFormatter'),
            (new ComponentBuilder())
                ->setNamespace('Monolog\Formatter')
                ->setSuffix('Formatter'),
            (new ComponentBuilder())
                ->setNamespace('Monolog\Processor')
                ->setSuffix('Processor')
        );
    }

    /** @var ComponentBuilder[]|array<string,ComponentBuilder> */
    private $components;

    /**
     * @param ComponentBuilder $channel
     * @param ComponentBuilder $handler
     * @param ComponentBuilder $formatter
     * @param ComponentBuilder $processor
     */
    public function __construct(
        ComponentBuilder $channel,
        ComponentBuilder $handler,
        ComponentBuilder $formatter,
        ComponentBuilder $processor
    ) {
        $this->components = [
            'channels' => $channel,
            'handlers' => $handler,
            'formatters' => $formatter,
            'processors' => $processor,
        ];
    }

    /**
     * @param array $config
     *
     * @return \Monolog\Logger|\Monolog\Handler\HandlerInterface|\Monolog\Formatter\FormatterInterface|callable
     *
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    public function build(array $config)
    {
        $default = [
            'class' => null,
            'type' => null,
            'arguments' => [],
            '_key' => null,
        ];
        $config = ArrayHelper::merge($default, $config);
        $componentBuilder = $this->components[$config['_key']] ?? null;
        if ($componentBuilder === null) {
            throw new \InvalidArgumentException('Invalid "_key" in configuration.');
        }
        return $componentBuilder->make($config['class'], $config['type'], $config['arguments']);
    }

    /**
     * @return string[]
     */
    public function getAvailableComponentKeys(): array
    {
        return array_keys($this->components);
    }

    /**
     * @param string $key
     *
     * @return string[]
     *
     * @throws \InvalidArgumentException
     */
    public function getDependencies(string $key): array
    {
        if (!isset($this->components[$key])) {
            throw new \InvalidArgumentException(sprintf('No component with key "%s" found.', $key));
        }
        return $this->components[$key]->getDependencies();
    }
}
