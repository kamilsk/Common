<?php

namespace OctoLab\Common\Tests\Monolog\Processor;

use OctoLab\Common\Monolog\Processor\TimeExecutionProcessor;

/**
 * phpunit src/Tests/Monolog/Processor/TimeExecutionProcessorTest.php
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class TimeExecutionProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function invoke()
    {
        $processor = new TimeExecutionProcessor();
        $record = $processor([]);
        self::assertNotEmpty($record['extra']);
        self::assertArrayHasKey('time_execution', $record['extra']);
        self::assertRegExp('/\d+\.\d{3}/', $record['extra']['time_execution']);
    }
}
