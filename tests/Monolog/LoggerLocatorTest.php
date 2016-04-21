<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog;

//use Monolog\Logger;
//use OctoLab\Common\Monolog\Processor\TimeExecutionProcessor;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class LoggerLocatorTest extends \PHPUnit_Framework_TestCase
{
//    /**
//     * @test
//     */
//    public function constructFailure()
//    {
//        try {
//            new LoggerLocator([
//                'channels' => [
//                    'app' => [
//                        'name' => 'APP',
//                        'handlers' => ['file'],
//                    ],
//                ],
//                'handlers' => [
//                    'file' => [
//                        'arguments' => [__DIR__ . '/info.log', 'info', true],
//                    ],
//                ],
//            ]);
//            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
//        } catch (\InvalidArgumentException $e) {
//            self::assertTrue(true);
//        }
//    }
//
//    /**
//     * @test
//     */
//    public function internals()
//    {
//        $locator = new LoggerLocator([
//            'channels' => [
//                'app' => [
//                    'arguments' => ['name' => 'APP'],
//                    'handlers' => ['file', 'chrome'],
//                ],
//                'db' => [
//                    'name' => 'app',
//                    'handlers' => ['file'],
//                    'processors' => ['memory', 'time'],
//                ],
//            ],
//            'handlers' => [
//                'file' => [
//                    'type' => 'stream',
//                    'arguments' => [__DIR__ . '/info.log', 'info', true],
//                    'formatter' => 'normal',
//                ],
//                'chrome' => [
//                    'type' => 'chrome_php',
//                    'arguments' => ['level' => 'info', 'bubble' => true],
//                    'formatter' => 'chrome',
//                ],
//            ],
//            'processors' => [
//                'memory' => [
//                    'type' => 'memory_usage',
//                ],
//                'time' => [
//                    'class' => TimeExecutionProcessor::class,
//                ],
//            ],
//            'formatters' => [
//                'normal' => [
//                    'type' => 'normalizer',
//                ],
//                'chrome' => [
//                    'type' => 'chrome_php',
//                ],
//            ],
//        ]);
//        $channel = $locator->getChannel('app');
//        self::assertEquals($channel, $locator->getDefaultChannel());
//        self::assertEquals('APP', $channel->getName());
//        self::assertCount(2, $channel->getHandlers());
//        /** @var \Monolog\Handler\ChromePHPHandler $handler */
//        $handler = $channel->getHandlers()[0];
//        self::assertInstanceOf('Monolog\Handler\ChromePHPHandler', $handler);
//        self::assertEquals(Logger::INFO, $handler->getLevel());
//        self::assertInstanceOf('Monolog\Formatter\ChromePHPFormatter', $handler->getFormatter());
//        /** @var \Monolog\Handler\StreamHandler $handler */
//        $handler = $channel->getHandlers()[1];
//        self::assertInstanceOf('Monolog\Handler\StreamHandler', $handler);
//        self::assertEquals(Logger::INFO, $handler->getLevel());
//        self::assertInstanceOf('Monolog\Formatter\NormalizerFormatter', $handler->getFormatter());
//
//        $channel = $locator->getChannel('db');
//        self::assertEquals('app', $channel->getName());
//        self::assertCount(1, $channel->getHandlers());
//        self::assertEquals($locator->getChannel('app')->getHandlers()[1], $channel->getHandlers()[0]);
//        self::assertCount(2, $channel->getProcessors());
//        self::assertInstanceOf(TimeExecutionProcessor::class, $channel->getProcessors()[0]);
//        self::assertInstanceOf('Monolog\Processor\MemoryUsageProcessor', $channel->getProcessors()[1]);
//    }
//
//    /**
//     * @test
//     * @dataProvider loggerLocatorProvider
//     *
//     * @param LoggerLocator $locator
//     */
//    public function getChannelSuccess(LoggerLocator $locator)
//    {
//        self::assertInstanceOf(Logger::class, $locator->getChannel('app'));
//    }
//
//    /**
//     * @test
//     * @dataProvider loggerLocatorProvider
//     * @expectedException \OutOfRangeException
//     *
//     * @param LoggerLocator $locator
//     */
//    public function getChannelFailure(LoggerLocator $locator)
//    {
//        self::assertInstanceOf(Logger::class, $locator->getChannel('unknown'));
//    }
//
//    /**
//     * @test
//     * @dataProvider loggerLocatorProvider
//     *
//     * @param LoggerLocator $locator
//     */
//    public function getDefaultChannel(LoggerLocator $locator)
//    {
//        self::assertInstanceOf(Logger::class, $locator->getDefaultChannel());
//        self::assertEquals($locator->getChannel('app'), $locator->getDefaultChannel());
//    }
//
//    /**
//     * @test
//     * @dataProvider loggerLocatorProvider
//     *
//     * @param LoggerLocator $locator
//     */
//    public function offsetExists(LoggerLocator $locator)
//    {
//        self::assertArrayHasKey('app', $locator);
//        self::assertArrayNotHasKey('unknown', $locator);
//    }
//
//    /**
//     * @test
//     * @dataProvider loggerLocatorProvider
//     *
//     * @param LoggerLocator $locator
//     */
//    public function offsetGet(LoggerLocator $locator)
//    {
//        self::assertEquals($locator->getChannel('app'), $locator['app']);
//    }
//
//    /**
//     * @test
//     * @dataProvider loggerLocatorProvider
//     * @expectedException \BadMethodCallException
//     *
//     * @param LoggerLocator $locator
//     */
//    public function offsetSet(LoggerLocator $locator)
//    {
//        $locator['db'] = $locator['app'];
//        self::assertEquals($locator['app'], $locator['db']);
//    }
//
//    /**
//     * @test
//     * @dataProvider loggerLocatorProvider
//     * @expectedException \BadMethodCallException
//     *
//     * @param LoggerLocator $locator
//     */
//    public function offsetUnset(LoggerLocator $locator)
//    {
//        unset($locator['app']);
//        self::assertArrayNotHasKey('app', $locator);
//    }
//
//    /**
//     * @test
//     * @dataProvider loggerLocatorProvider
//     *
//     * @param LoggerLocator $locator
//     */
//    public function countTest(LoggerLocator $locator)
//    {
//        self::assertCount(2, $locator);
//    }
//
//    /**
//     * @test
//     * @dataProvider loggerLocatorProvider
//     * @expectedException \OutOfRangeException
//     *
//     * @param LoggerLocator $locator
//     */
//    public function current(LoggerLocator $locator)
//    {
//        $locator->next();
//        $locator->next();
//        $locator->current();
//    }
//
//    /**
//     * @test
//     * @dataProvider loggerLocatorProvider
//     *
//     * @param LoggerLocator $locator
//     */
//    public function iterator(LoggerLocator $locator)
//    {
//        foreach ($locator as $key => $channel) {
//            self::assertInstanceOf(Logger::class, $channel);
//        }
//    }
//
//    /**
//     * @return array<int, LoggerLocator[]>
//     */
//    public function loggerLocatorProvider()
//    {
//        return [
//            [new LoggerLocator([
//                'channels' => [
//                    'app' => ['name' => 'APP'],
//                    'debug' => [],
//                ],
//            ])],
//        ];
//    }
}
