<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog\Handler;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class BulletproofStreamHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider loggerProvider
     *
     * @param Logger $logger
     */
    public function writeToMovedLogFile(Logger $logger)
    {
        $streamLocation = $this->getStreamLocation();
        $newStreamLocation = $this->getNewStreamLocation();
        $logger->pushHandler(new StreamHandler($streamLocation));
        $logger->info('Start logging.');
        self::assertFileExists($streamLocation);
        $this->mv($streamLocation, $newStreamLocation);
        self::assertFileNotExists($streamLocation);
        self::assertFileExists($newStreamLocation);
        $logger->info('End logging.');
        $content = file_get_contents($newStreamLocation);
        self::assertContains('Start logging.', $content);
        self::assertContains('End logging.', $content);
        $this->rm($newStreamLocation);
    }

    /**
     * @test
     * @dataProvider loggerProvider
     *
     * @param Logger $logger
     */
    public function bulletproofWriteToMovedLogFile(Logger $logger)
    {
        $streamLocation = $this->getStreamLocation();
        $newStreamLocation = $this->getNewStreamLocation();
        $logger->pushHandler(new BulletproofStreamHandler($streamLocation));
        $logger->info('Start logging.');
        self::assertFileExists($streamLocation);
        $this->mv($streamLocation, $newStreamLocation);
        self::assertFileNotExists($streamLocation);
        self::assertFileExists($newStreamLocation);
        $logger->info('End logging.');
        self::assertFileExists($streamLocation);
        self::assertContains('Start logging.', file_get_contents($newStreamLocation));
        self::assertContains('End logging.', file_get_contents($streamLocation));
        $this->rm($streamLocation);
        $this->rm($newStreamLocation);
    }

    /**
     * @test
     * @dataProvider loggerProvider
     *
     * @param Logger $logger
     */
    public function writeToRemovedLogFile(Logger $logger)
    {
        $streamLocation = $this->getStreamLocation();
        $logger->pushHandler(new StreamHandler($streamLocation));
        $logger->info('Start logging.');
        self::assertFileExists($streamLocation);
        $this->rm($streamLocation);
        self::assertFileNotExists($streamLocation);
        $logger->info('End logging.');
        self::assertFileNotExists($streamLocation);
    }

    /**
     * @test
     * @dataProvider loggerProvider
     *
     * @param Logger $logger
     */
    public function bulletproofWriteToRemovedLogFile(Logger $logger)
    {
        $streamLocation = $this->getStreamLocation();
        $logger->pushHandler(new BulletproofStreamHandler($streamLocation));
        $logger->info('Start logging.');
        self::assertFileExists($streamLocation);
        $this->rm($streamLocation);
        self::assertFileNotExists($streamLocation);
        $logger->info('End logging.');
        self::assertFileExists($streamLocation);
        self::assertContains('End logging.', file_get_contents($streamLocation));
        $this->rm($streamLocation);
    }

    /**
     * @return array
     */
    public function loggerProvider(): array
    {
        return [
            [new Logger('test')],
        ];
    }

    /**
     * @return string
     */
    private function getStreamLocation(): string
    {
        return __DIR__ . '/test.log';
    }

    /**
     * @return string
     */
    private function getNewStreamLocation(): string
    {
        return __DIR__ . '/moved.log';
    }

    /**
     * @param string $file
     */
    private function rm(string $file)
    {
        shell_exec(sprintf('rm %s', escapeshellarg($file)));
    }

    /**
     * @param string $file
     * @param string $location
     */
    private function mv(string $file, string $location)
    {
        shell_exec(sprintf('mv %s %s', escapeshellarg($file), escapeshellarg($location)));
    }
}
