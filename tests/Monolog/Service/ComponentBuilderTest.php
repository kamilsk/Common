<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog\Service;

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use OctoLab\Common\Monolog\Handler\DesktopNotificationHandler;
use OctoLab\Common\Monolog\Processor\TimeExecutionProcessor;
use OctoLab\Common\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class ComponentBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function makeLogger()
    {
        $componentBuilder = (new ComponentBuilder())
            ->setClass('Monolog\Logger')
            ->addDependency('handlers', 'pushHandler')
            ->addDependency('processors', 'pushProcessor')
        ;
        $dependencies = [
            'handlers' => 'pushHandler',
            'processors' => 'pushProcessor',
        ];
        self::assertEquals($dependencies, $componentBuilder->getDependencies());
        $logger = $componentBuilder->make(null, null, ['name' => 'test']);
        self::assertInstanceOf(LoggerInterface::class, $logger);
        self::assertInstanceOf(Logger::class, $logger);
        try {
            $componentBuilder->make();
            self::fail(sprintf('%s exception expected.', \PHPUnit_Framework_Error_Warning::class));
        } catch (\PHPUnit_Framework_Error_Warning $e) {
            self::assertContains('Missing argument 1 for Monolog\Logger::__construct()', $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function makeHandler()
    {
        $componentBuilder = (new ComponentBuilder())
            ->setNamespace('Monolog\Handler')
            ->setSuffix('Handler')
            ->addDependency('processors', 'pushProcessor')
            ->addDependency('formatter', 'setFormatter')
        ;
        $dependencies = [
            'processors' => 'pushProcessor',
            'formatter' => 'setFormatter',
        ];
        self::assertEquals($dependencies, $componentBuilder->getDependencies());
        $handler = $componentBuilder->make(null, 'test');
        self::assertInstanceOf(HandlerInterface::class, $handler);
        self::assertInstanceOf(TestHandler::class, $handler);
        try {
            $componentBuilder->make(null, 'php_console');
            self::fail(sprintf('%s exception expected.', \Exception::class));
        } catch (\Exception $e) {
            self::assertContains('PHP Console library not found.', $e->getMessage());
        }
        $handler = $componentBuilder->make(DesktopNotificationHandler::class, null, ['name' => 'test']);
        self::assertInstanceOf(HandlerInterface::class, $handler);
        self::assertInstanceOf(DesktopNotificationHandler::class, $handler);
    }

    /**
     * @test
     */
    public function makeFormatter()
    {
        $componentBuilder = (new ComponentBuilder())
            ->setNamespace('Monolog\Formatter')
            ->setSuffix('Formatter')
        ;
        $formatter = $componentBuilder->make(null, 'json');
        self::assertInstanceOf(FormatterInterface::class, $formatter);
        self::assertInstanceOf(JsonFormatter::class, $formatter);
    }

    /**
     * @test
     */
    public function makeProcessor()
    {
        $componentBuilder = (new ComponentBuilder())
            ->setNamespace('Monolog\Processor')
            ->setSuffix('Processor')
        ;
        $processor = $componentBuilder->make(null, 'uid');
        self::assertTrue(is_callable($processor));
        self::assertInstanceOf(UidProcessor::class, $processor);
        $componentBuilder->setClass(TimeExecutionProcessor::class);
        $processor = $componentBuilder->make();
        self::assertTrue(is_callable($processor));
        self::assertInstanceOf(TimeExecutionProcessor::class, $processor);
    }

    /**
     * @test
     */
    public function makeFailure()
    {
        $componentBuilder = new ComponentBuilder();
        try {
            $componentBuilder->make();
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
            self::assertContains('Component type is not provided.', $e->getMessage());
        }
    }
}
