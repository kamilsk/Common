<?php

namespace Test\OctoLab\Common\Monolog\Util;

use Monolog\Formatter\ChromePHPFormatter;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\ChromePHPHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\MemoryPeakUsageProcessor;
use Monolog\Processor\UidProcessor;
use OctoLab\Common\Config\Loader\YamlFileLoader;
use OctoLab\Common\Config\Parser\SymfonyYamlParser;
use OctoLab\Common\Config\YamlConfig;
use OctoLab\Common\Monolog\Processor\TimeExecutionProcessor;
use OctoLab\Common\Monolog\Util\ConfigResolver;
use Symfony\Component\Config\FileLocator;
use Test\OctoLab\Common\TestCase;

/**
 * phpunit tests/Monolog/Util/ConfigResolverTest.php
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class ConfigResolverTest extends TestCase
{
    /**
     * @return array[]
     */
    public function resolverProvider()
    {
        return [
            [new ConfigResolver()],
        ];
    }

    /**
     * @test
     * @dataProvider resolverProvider
     *
     * @param ConfigResolver $resolver
     */
    public function getChannels(ConfigResolver $resolver)
    {
        self::assertCount(0, $resolver->getChannels());
    }

    /**
     * @test
     * @dataProvider resolverProvider
     *
     * @param ConfigResolver $resolver
     */
    public function getHandlers(ConfigResolver $resolver)
    {
        self::assertCount(0, $resolver->getHandlers());
    }

    /**
     * @test
     * @dataProvider resolverProvider
     *
     * @param ConfigResolver $resolver
     */
    public function getProcessors(ConfigResolver $resolver)
    {
        self::assertCount(0, $resolver->getProcessors());
    }

    /**
     * @test
     * @dataProvider resolverProvider
     *
     * @param ConfigResolver $resolver
     */
    public function getFormatters(ConfigResolver $resolver)
    {
        self::assertCount(0, $resolver->getFormatters());
    }

    /**
     * @test
     * @dataProvider resolverProvider
     *
     * @param ConfigResolver $resolver
     */
    public function resolve(ConfigResolver $resolver)
    {
        $config = (new YamlConfig(new YamlFileLoader(new FileLocator(), new SymfonyYamlParser())))
            ->load($this->getConfigPath('monolog/config'))
            ->replace(['root_dir' => dirname(__DIR__)])
            ->toArray()
        ;
        $resolver->resolve($config['monolog']);
        self::assertCount(0, $resolver->getFormatters());
        self::assertCount(0, $resolver->getProcessors());
        self::assertCount(2, $resolver->getHandlers());
        foreach ($resolver->getHandlers() as $handler) {
            self::assertInstanceOf(HandlerInterface::class, $handler);
        }
        self::assertCount(1, $resolver->getChannels());
        foreach ($resolver->getChannels() as $channel) {
            self::assertInstanceOf(Logger::class, $channel);
        }
        $channel = $resolver->getDefaultChannel();
        self::assertNotFalse($channel);
        self::assertEquals('app', $channel->getName());
        self::assertCount(2, $channel->getHandlers());
        foreach ($channel->getHandlers() as $handler) {
            self::assertInstanceOf(StreamHandler::class, $handler);
            self::assertInstanceOf(JsonFormatter::class, $handler->getFormatter());
        }
        self::assertEquals($resolver->getHandlers()['error'], $channel->getHandlers()[0]);
        self::assertEquals($resolver->getHandlers()['access'], $channel->getHandlers()[1]);
        self::assertCount(3, $channel->getProcessors());
        self::assertInstanceOf(TimeExecutionProcessor::class, $channel->getProcessors()[0]);
        self::assertInstanceOf(UidProcessor::class, $channel->getProcessors()[1]);
        self::assertInstanceOf(MemoryPeakUsageProcessor::class, $channel->getProcessors()[2]);
    }

    /**
     * @test
     * @dataProvider resolverProvider
     *
     * @param ConfigResolver $resolver
     */
    public function resolveCascade(ConfigResolver $resolver)
    {
        $config = (new YamlConfig(new YamlFileLoader(new FileLocator(), new SymfonyYamlParser())))
            ->load($this->getConfigPath('monolog/cascade'))
            ->replace(['root_dir' => dirname(__DIR__)])
            ->toArray()
        ;
        $resolver->resolve($config['monolog']);
        self::assertCount(1, $resolver->getFormatters());
        foreach ($resolver->getFormatters() as $formatter) {
            self::assertInstanceOf(FormatterInterface::class, $formatter);
        }
        self::assertCount(1, $resolver->getProcessors());
        foreach ($resolver->getProcessors() as $processor) {
            self::assertTrue(is_callable($processor));
        }
        self::assertCount(1, $resolver->getHandlers());
        foreach ($resolver->getHandlers() as $handler) {
            self::assertInstanceOf(HandlerInterface::class, $handler);
        }
        self::assertCount(2, $resolver->getChannels());
        foreach ($resolver->getChannels() as $channel) {
            self::assertInstanceOf(Logger::class, $channel);
        }
        $channel = $resolver->getDefaultChannel();
        self::assertNotFalse($channel);
        self::assertEquals('logger', $channel->getName());
        self::assertEquals($resolver->getChannels()['logger'], $channel);
        self::assertCount(2, $channel->getHandlers());
        self::assertEquals($resolver->getHandlers()['info'], $channel->getHandlers()[0]);
        self::assertInstanceOf(StreamHandler::class, $channel->getHandlers()[0]);
        self::assertInstanceOf(JsonFormatter::class, $channel->getHandlers()[0]->getFormatter());
        self::assertInstanceOf(ChromePHPHandler::class, $channel->getHandlers()[1]);
        self::assertInstanceOf(ChromePHPFormatter::class, $channel->getHandlers()[1]->getFormatter());
        self::assertCount(0, $channel->getProcessors());
        $processor = $channel->getHandlers()[0]->popProcessor();
        self::assertInstanceOf(UidProcessor::class, $processor);
        try {
            $channel->getHandlers()[0]->popProcessor();
            self::assertTrue(false);
        } catch (\LogicException $e) {
            self::assertTrue(true);
        }
        $channel->getHandlers()[0]->pushProcessor($processor);
        $dbChannel = $resolver->getChannels()['db'];
        self::assertEquals('logger', $dbChannel->getName());
        self::assertCount(1, $dbChannel->getHandlers());
        self::assertEquals($resolver->getHandlers()['info'], $dbChannel->getHandlers()[0]);
        self::assertInstanceOf(StreamHandler::class, $dbChannel->getHandlers()[0]);
        self::assertInstanceOf(JsonFormatter::class, $dbChannel->getHandlers()[0]->getFormatter());
        self::assertCount(1, $dbChannel->getProcessors());
        self::assertEquals($resolver->getProcessors()['memory'], $dbChannel->getProcessors()[0]);
        self::assertInstanceOf(MemoryPeakUsageProcessor::class, $dbChannel->getProcessors()[0]);
    }

    /**
     * @test
     * @dataProvider resolverProvider
     * @expectedException \InvalidArgumentException
     *
     * @param ConfigResolver $resolver
     */
    public function throwInvalidArgumentExceptionByGetClass(ConfigResolver $resolver)
    {
        $resolver->resolve([
            'handlers' => [
                'stream' => [],
            ],
        ]);
    }
}
