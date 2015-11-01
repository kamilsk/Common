<?php

namespace OctoLab\Common\Tests\Monolog\Handler;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use OctoLab\Common\Monolog\Handler\BulletproofStreamHandler;

/**
 * phpunit src/Tests/Monolog/Handler/BulletproofStreamHandlerTest.php
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class BulletproofStreamHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function write()
    {
        $logger = new Logger('test');
        $logger->pushHandler(new StreamHandler($this->getStream()));
        $logger->info('Start logging.');
        self::assertFileExists($this->getStream());
        $this->rm($this->getStream());
        $logger->info('End logging.');
        self::assertFileNotExists($this->getStream());
    }

    /**
     * @test
     */
    public function writeInMovedFile()
    {
        $newLocation = $this->getBasePath() . '/moved.log';
        $logger = new Logger('test');
        $logger->pushHandler(new StreamHandler($this->getStream()));
        $logger->info('Start logging.');
        self::assertFileExists($this->getStream());
        exec(sprintf('mv %s %s', escapeshellarg($this->getStream()), escapeshellarg($newLocation)));
        self::assertFileNotExists($this->getStream());
        $logger->info('Continue logging.');
        $content = file_get_contents($newLocation);
        self::assertContains('Start logging.', $content);
        self::assertContains('Continue logging.', $content);
        $this->rm($newLocation);
    }

    /**
     * @test
     */
    public function bulletproofWrite()
    {
        $logger = new Logger('test');
        $logger->pushHandler(new BulletproofStreamHandler($this->getStream(), Logger::INFO, true, 0644));
        $logger->info('Start logging.');
        self::assertFileExists($this->getStream());
        $this->rm($this->getStream());
        $logger->info('End logging.');
        self::assertFileExists($this->getStream());
        self::assertContains('End logging.', file_get_contents($this->getStream()));
        $this->rm($this->getStream());
    }

    /**
     * @return string
     */
    private function getStream()
    {
        return $this->getBasePath() . '/test.log';
    }

    /**
     * @return string
     */
    private function getBasePath()
    {
        return dirname(dirname(__DIR__)) . '/data';
    }

    /**
     * @param string $file
     */
    private function rm($file)
    {
        exec('rm ' . escapeshellarg($file));
    }
}
