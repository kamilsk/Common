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
        unlink($this->getStream());
        $logger->info('End logging.');
        self::assertFileNotExists($this->getStream());
    }

    /**
     * @test
     */
    public function bulletproofWrite()
    {
        $logger = new Logger('test');
        $logger->pushHandler(new BulletproofStreamHandler($this->getStream()));
        $logger->info('Start logging.');
        self::assertFileExists($this->getStream());
        unlink($this->getStream());
        $logger->info('End logging.');
        self::assertFileExists($this->getStream());
        self::assertContains('End logging.', file_get_contents($this->getStream()));
        unlink($this->getStream());
    }

    /**
     * @return string
     */
    private function getStream()
    {
        return dirname(dirname(__DIR__)) . '/data/test.log';
    }
}
