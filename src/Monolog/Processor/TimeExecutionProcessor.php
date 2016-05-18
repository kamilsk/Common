<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog\Processor;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class TimeExecutionProcessor
{
    /** @var float */
    private $started;

    /**
     * @api
     */
    public function __construct()
    {
        $this->started = microtime(true);
    }

    /**
     * @param array $record
     *
     * @return array
     *
     * @api
     */
    public function __invoke(array $record): array
    {
        $record['extra']['time_execution'] = sprintf('%01.3f', microtime(true) - $this->started);
        return $record;
    }
}
