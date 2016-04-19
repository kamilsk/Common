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
        $logger = new Logger('test');
        $logger->pushHandler(new DesktopNotificationHandler($logger->getName()));
        $logger->pushProcessor(new TimeExecutionProcessor());
        self::assertTrue($logger->info('Test message'));
    }
}
