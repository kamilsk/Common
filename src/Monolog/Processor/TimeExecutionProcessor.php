<?php

namespace OctoLab\Common\Monolog\Processor;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class TimeExecutionProcessor
{
    /** @var float */
    private $started;

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
    public function __invoke(array $record)
    {
        $record['extra']['time_execution'] = sprintf('%01.3f', microtime(true) - $this->started);
        return $record;
    }
}
