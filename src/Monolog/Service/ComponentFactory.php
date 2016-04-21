<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog\Service;

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
            (new Component())
                ->setClass('Monolog\Logger')
                ->addDependency('handlers', 'pushHandler')
                ->addDependency('processors', 'pushProcessor'),
            (new Component())
                ->setNamespace('Monolog\Handler')
                ->setSuffix('Handler')
                ->addDependency('processors', 'pushProcessor')
                ->addDependency('formatter', 'setFormatter'),
            (new Component())
                ->setNamespace('Monolog\Formatter')
                ->setSuffix('Formatter'),
            (new Component())
                ->setNamespace('Monolog\Processor')
                ->setSuffix('Processor')
        );
    }

    /** @var Component[] */
    private $components;

    /**
     * @param Component $channel
     * @param Component $handler
     * @param Component $formatter
     * @param Component $processor
     */
    public function __construct(
        Component $channel,
        Component $handler,
        Component $formatter,
        Component $processor
    ) {
        $this->components = [
            'channels' => $channel,
            'handlers' => $handler,
            'formatters' => $formatter,
            'processors' => $processor,
        ];
    }
}
