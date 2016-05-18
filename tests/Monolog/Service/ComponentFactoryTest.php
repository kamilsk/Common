<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog\Service;

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use OctoLab\Common\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class ComponentFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function buildSuccess()
    {
        $componentFactory = ComponentFactory::withDefaults();
        $logger = $componentFactory->build(['arguments' => ['test'], '_key' => 'channels']);
        self::assertInstanceOf(LoggerInterface::class, $logger);
        self::assertInstanceOf(Logger::class, $logger);
        $handler = $componentFactory->build(['type' => 'test', '_key' => 'handlers']);
        self::assertInstanceOf(HandlerInterface::class, $handler);
        self::assertInstanceOf(TestHandler::class, $handler);
        $formatter = $componentFactory->build(['type' => 'json', '_key' => 'formatters']);
        self::assertInstanceOf(FormatterInterface::class, $formatter);
        self::assertInstanceOf(JsonFormatter::class, $formatter);
        $processor = $componentFactory->build(['type' => 'uid', '_key' => 'processors']);
        self::assertTrue(is_callable($processor));
        self::assertInstanceOf(UidProcessor::class, $processor);
    }

    /**
     * @test
     */
    public function buildFailure()
    {
        $componentFactory = ComponentFactory::withDefaults();
        try {
            $componentFactory->build(['_key' => 'unknown']);
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
            self::assertContains('Invalid "_key:unknown" in configuration.', $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function getAvailableComponentKeys()
    {
        $componentFactory = ComponentFactory::withDefaults();
        self::assertEquals(
            ['channels', 'handlers', 'formatters', 'processors'],
            $componentFactory->getAvailableComponentKeys()
        );
    }

    /**
     * @test
     */
    public function getDependenciesSuccess()
    {
        $componentFactory = ComponentFactory::withDefaults();
        $dependencies = [
            'handlers' => 'pushHandler',
            'processors' => 'pushProcessor',
        ];
        self::assertEquals($dependencies, $componentFactory->getDependencies('channels'));
        $dependencies = [
            'processors' => 'pushProcessor',
            'formatter' => 'setFormatter',
        ];
        self::assertEquals($dependencies, $componentFactory->getDependencies('handlers'));
        self::assertEmpty($componentFactory->getDependencies('formatters'));
        self::assertEmpty($componentFactory->getDependencies('processors'));
    }

    /**
     * @test
     */
    public function getDependenciesFailure()
    {
        $componentFactory = ComponentFactory::withDefaults();
        try {
            $componentFactory->getDependencies('unknown');
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
            self::assertContains('Component with key "unknown" not found.', $e->getMessage());
        }
    }
}
