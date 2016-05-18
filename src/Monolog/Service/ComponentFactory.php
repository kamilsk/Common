<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog\Service;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class ComponentFactory
{
    /** @var ComponentBuilder[]|array<string,ComponentBuilder> */
    private $components;

    /**
     * @param ComponentBuilder $channelBuilder
     * @param ComponentBuilder $handlerBuilder
     * @param ComponentBuilder $formatterBuilder
     * @param ComponentBuilder $processorBuilder
     *
     * @api
     */
    public function __construct(
        ComponentBuilder $channelBuilder,
        ComponentBuilder $handlerBuilder,
        ComponentBuilder $formatterBuilder,
        ComponentBuilder $processorBuilder
    ) {
        $this->components = [
            'channels' => $channelBuilder,
            'handlers' => $handlerBuilder,
            'formatters' => $formatterBuilder,
            'processors' => $processorBuilder,
        ];
    }

    /**
     * @return ComponentFactory
     *
     * @api
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

    /**
     * @param array $config
     *
     * @return \Monolog\Logger|\Monolog\Handler\HandlerInterface|\Monolog\Formatter\FormatterInterface|callable
     *
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     *
     * @api
     */
    public function build(array $config)
    {
        $default = [
            'class' => null,
            'type' => null,
            'arguments' => [],
            '_key' => null,
        ];
        $config = array_merge($default, $config);
        $componentBuilder = $this->components[$config['_key']] ?? null;
        if ($componentBuilder === null) {
            throw new \InvalidArgumentException(sprintf('Invalid "_key:%s" in configuration.', $config['_key']));
        }
        return $componentBuilder->make($config['class'], $config['type'], $config['arguments']);
    }

    /**
     * @return string[]
     *
     * @api
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
     *
     * @api
     */
    public function getDependencies(string $key): array
    {
        if (!isset($this->components[$key])) {
            throw new \InvalidArgumentException(sprintf('Component with key "%s" not found.', $key));
        }
        return $this->components[$key]->getDependencies();
    }
}
