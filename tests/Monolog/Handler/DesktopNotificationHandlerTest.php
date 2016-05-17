<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog\Handler;

use Monolog\Logger;
use OctoLab\Common\Monolog\Processor\TimeExecutionProcessor;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class DesktopNotificationHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function write()
    {
        $logger = new Logger('test', [new DesktopNotificationHandler('test')], [new TimeExecutionProcessor()]);
        self::assertTrue($logger->info('Test message'));
    }

    /**
     * @test
     */
    public function issue49()
    {
        $logger = new Logger('test', [new DesktopNotificationHandler('test', 'error')]);
        self::assertFalse($logger->info('Test message'));
        self::assertTrue($logger->error('Test message'));
        $logger = new Logger('test', [new DesktopNotificationHandler('test', Logger::ERROR)]);
        self::assertFalse($logger->info('Test message'));
        self::assertTrue($logger->error('Test message'));
    }
}
