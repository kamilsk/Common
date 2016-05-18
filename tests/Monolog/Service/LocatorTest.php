<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog\Service;

use Monolog\Logger;
use OctoLab\Common\Monolog\Processor\TimeExecutionProcessor;
use OctoLab\Common\TestCase;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class LocatorTest extends TestCase
{
    /**
     * @test
     */
    public function complex()
    {
        $locator = new Locator(
            [
                'channels' => [
                    'app' => [
                        'arguments' => ['name' => 'APP'],
                        'handlers' => ['file', 'chrome'],
                    ],
                    'db' => [
                        'name' => 'app',
                        'handlers' => ['file'],
                        'processors' => ['memory', 'time'],
                    ],
                ],
                'handlers' => [
                    'file' => [
                        'type' => 'stream',
                        'arguments' => [__DIR__ . '/info.log', 'info', true],
                        'formatter' => 'normal',
                    ],
                    'chrome' => [
                        'type' => 'chrome_php',
                        'arguments' => ['level' => 'info', 'bubble' => true],
                        'formatter' => 'chrome',
                    ],
                ],
                'processors' => [
                    'memory' => [
                        'type' => 'memory_usage',
                    ],
                    'time' => [
                        'class' => TimeExecutionProcessor::class,
                    ],
                ],
                'formatters' => [
                    'normal' => [
                        'type' => 'normalizer',
                    ],
                    'chrome' => [
                        'type' => 'chrome_php',
                    ],
                ],
            ],
            ComponentFactory::withDefaults()
        );
        $channel = $locator->getChannel('app');
        self::assertEquals($channel, $locator->getDefaultChannel());
        self::assertEquals('APP', $channel->getName());
        self::assertCount(2, $channel->getHandlers());
        /** @var \Monolog\Handler\ChromePHPHandler $handler */
        $handler = $channel->getHandlers()[0];
        self::assertInstanceOf('Monolog\Handler\ChromePHPHandler', $handler);
        self::assertEquals(Logger::INFO, $handler->getLevel());
        self::assertInstanceOf('Monolog\Formatter\ChromePHPFormatter', $handler->getFormatter());
        /** @var \Monolog\Handler\StreamHandler $handler */
        $handler = $channel->getHandlers()[1];
        self::assertInstanceOf('Monolog\Handler\StreamHandler', $handler);
        self::assertEquals(Logger::INFO, $handler->getLevel());
        self::assertInstanceOf('Monolog\Formatter\NormalizerFormatter', $handler->getFormatter());
        $channel = $locator->getChannel('db');
        self::assertEquals('app', $channel->getName());
        self::assertCount(1, $channel->getHandlers());
        self::assertEquals($locator->getChannel('app')->getHandlers()[1], $channel->getHandlers()[0]);
        self::assertCount(2, $channel->getProcessors());
        self::assertInstanceOf(TimeExecutionProcessor::class, $channel->getProcessors()[0]);
        self::assertInstanceOf('Monolog\Processor\MemoryUsageProcessor', $channel->getProcessors()[1]);
    }

    /**
     * @test
     */
    public function constructFailure()
    {
        try {
            new Locator([], ComponentFactory::withDefaults());
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
            self::assertContains('Channels not found.', $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function getChannelSuccess()
    {
        self::assertInstanceOf(Logger::class, $this->getLocator()->getChannel('app'));
    }

    /**
     * @test
     * @expectedException \OutOfRangeException
     * @expectedExceptionMessage Component with ID "channel.unknown" not found.
     */
    public function getChannelFailure()
    {
        self::assertInstanceOf(Logger::class, $this->getLocator()->getChannel('unknown'));
    }

    /**
     * @test
     */
    public function getDefaultChannelSuccess()
    {
        $locator = $this->getLocator();
        self::assertInstanceOf(Logger::class, $locator->getDefaultChannel());
        self::assertEquals($locator->getChannel('app'), $locator->getDefaultChannel());
    }

    /**
     * @test
     * @expectedException \OutOfRangeException
     * @expectedExceptionMessage Component with ID "channel.unknown" not found.
     */
    public function getDefaultChannelFailure()
    {
        $locator = new Locator(
            [
                'channels' => [
                    'app' => ['name' => 'APP'],
                    'debug' => [],
                ],
                'default_channel' => 'unknown',
            ],
            ComponentFactory::withDefaults()
        );
        self::assertInstanceOf(Logger::class, $locator->getDefaultChannel());
    }

    /**
     * @test
     */
    public function offsetExists()
    {
        $locator = $this->getLocator();
        self::assertArrayHasKey('app', $locator);
        self::assertArrayNotHasKey('unknown', $locator);
    }

    /**
     * @test
     */
    public function offsetGet()
    {
        $locator = $this->getLocator();
        self::assertEquals($locator->getChannel('app'), $locator['app']);
    }

    /**
     * @test
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Storage is read-only.
     */
    public function offsetSet()
    {
        $locator = $this->getLocator();
        $locator['db'] = $locator['app'];
        self::assertEquals($locator['app'], $locator['db']);
    }

    /**
     * @test
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Storage is read-only.
     */
    public function offsetUnset()
    {
        $locator = $this->getLocator();
        unset($locator['app']);
        self::assertArrayNotHasKey('app', $locator);
    }

    /**
     * @test
     */
    public function countTest()
    {
        self::assertCount(2, $this->getLocator());
    }

    /**
     * @test
     */
    public function currentSuccess()
    {
        self::assertInstanceOf(Logger::class, $this->getLocator()->current());
    }

    /**
     * @test
     * @expectedException \OutOfRangeException
     * @expectedExceptionMessage Current position of pointer is out of range.
     */
    public function currentFailure()
    {
        $locator = $this->getLocator();
        $locator->next();
        $locator->next();
        self::assertInstanceOf(Logger::class, $locator->current());
    }

    /**
     * @test
     */
    public function iterator()
    {
        $keys = ['app', 'debug'];
        $locator = $this->getLocator();
        for ($i = 0; $i < 2; $i++) {
            foreach ($locator as $key => $channel) {
                self::assertInstanceOf(Logger::class, $channel);
                self::assertEquals(current($keys), $key);
                self::assertTrue($locator->valid());
                next($keys);
            }
            $locator->next();
            self::assertFalse($locator->valid());
            reset($keys);
            $locator->rewind();
        }
    }

    /**
     * @test
     */
    public function issue51()
    {
        $locator = new Locator(
            [
                'channels' => [
                    'db' => ['handlers' => ['queries']],
                ],
                'handlers' => [
                    'queries' => [
                        'type' => 'buffer',
                        'arguments' => ['@handler.chrome', 10, 'info'],
                    ],
                    'chrome' => ['type' => 'chrome_php'],
                ],
            ],
            ComponentFactory::withDefaults()
        );
        $channel = $locator->getDefaultChannel();
        self::assertCount(1, $channel->getHandlers());
        self::assertInstanceOf('Monolog\Handler\BufferHandler', $channel->getHandlers()[0]);
    }

    /**
     * @return Locator
     */
    private function getLocator(): Locator
    {
        return new Locator(
            [
                'channels' => [
                    'app' => ['name' => 'APP'],
                    'debug' => [],
                ],
            ],
            ComponentFactory::withDefaults()
        );
    }
}
