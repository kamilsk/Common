<?php

namespace OctoLab\Common\Monolog\Processor;

use OctoLab\Common\TestCase;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class SignProcessorTest extends TestCase
{
    /**
     * @test
     */
    public function invoke()
    {
        $sign = '00000000-0000-0000-0000-000000000000';
        $processor = new SignProcessor($sign);
        $record = $processor([]);
        self::assertNotEmpty($record['extra']);
        self::assertArrayHasKey('sign', $record['extra']);
        self::assertEquals($sign, $record['extra']['sign']);
    }
}
