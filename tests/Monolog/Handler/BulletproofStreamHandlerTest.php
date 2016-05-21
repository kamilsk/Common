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
     */
    public function bulletproofWriteToMovedLogFile()
    {
        $logger = $this->getLogger();
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
     */
    public function bulletproofWriteToRemovedLogFile()
    {
        $logger = $this->getLogger();
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
     * @test
     */
    public function writeToMovedLogFile()
    {
        $logger = $this->getLogger();
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
     */
    public function writeToRemovedLogFile()
    {
        $logger = $this->getLogger();
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
     * @return Logger
     */
    private function getLogger(): Logger
    {
        return new Logger('test');
    }

    /**
     * @return string
     */
    private function getNewStreamLocation(): string
    {
        return __DIR__ . '/moved.log';
    }

    /**
     * @return string
     */
    private function getStreamLocation(): string
    {
        return __DIR__ . '/test.log';
    }

    /**
     * @param string $file
     * @param string $location
     */
    private function mv(string $file, string $location)
    {
        shell_exec(sprintf('mv %s %s', escapeshellarg($file), escapeshellarg($location)));
    }

    /**
     * @param string $file
     */
    private function rm(string $file)
    {
        shell_exec(sprintf('rm %s', escapeshellarg($file)));
    }
}
