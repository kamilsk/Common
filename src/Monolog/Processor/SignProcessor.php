<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog\Processor;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class SignProcessor
{
    /** @var string */
    private $sign;

    /**
     * @param string $sign
     *
     * @api
     */
    public function __construct($sign)
    {
        $this->sign = $sign;
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
        $record['extra']['sign'] = $this->sign;
        return $record;
    }
}
