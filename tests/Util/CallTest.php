<?php

declare(strict_types = 1);

namespace OctoLab\Common\Util;

use OctoLab\Common\TestCase;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class CallTest extends TestCase
{
    /**
     * @test
     */
    public function begin()
    {
        self::assertInstanceOf(Call::class, Call::begin([$this, 'begin']));
    }

    /**
     * @test
     */
    public function rescue()
    {
        $sugar = Call::begin(function ($message, $code = 0) {
            throw new \Exception($message, $code);
        });
        try {
            $sugar->rescue()->end('Handled exception!');
            self::assertTrue(true);
        } catch (\Exception $e) {
            self::assertTrue(false);
        }
    }

    /**
     * @test
     */
    public function retry()
    {
        $times = 0;
        $sugar = Call::begin(function ($message, $code = 0) use (&$times) {
            $times++;
            throw new \Exception($message, $code);
        });
        try {
            $sugar->rescue()->retry(3)->end('Handled exception three times!');
            self::assertEquals(3, $times);
        } catch (\Exception $e) {
            self::assertTrue(false);
        }
    }

    /**
     * @test
     */
    public function end()
    {
        $sugar = Call::begin(function ($message, $code = 0) {
            throw new \RuntimeException($message, $code);
        });
        try {
            $sugar->end('Unhandled exception.');
            self::assertTrue(false);
        } catch (\RuntimeException $e) {
            self::assertTrue(true);
        }
    }

    /**
     * @test
     */
    public function cases()
    {
        $exceptionGenerator = function ($code) {
            switch ($code) {
                case 1:
                    throw new \RuntimeException();
                case 2:
                    throw new \LogicException();
                default:
                    echo 'Default!', PHP_EOL;
                    throw new \Exception();
            }
        };
        try {
            Call::begin($exceptionGenerator)->end(1);
            self::assertTrue(false);
        } catch (\RuntimeException $e) {
            self::assertTrue(true);
        }
        try {
            ob_clean();
            ob_start();
            Call::begin($exceptionGenerator)
                ->rescue(\LogicException::class, function () {
                    echo 'Success!';
                })
                ->end(2)
            ;
            self::assertContains('Success!', ob_get_clean());
        } catch (\LogicException $e) {
            self::assertTrue(false);
        }
        try {
            ob_clean();
            ob_start();
            Call::begin($exceptionGenerator)
                ->rescue()
                ->retry(2)
                ->end(3)
            ;
            self::assertContains("Default!\nDefault!", ob_get_clean());
        } catch (\Exception $e) {
            self::assertTrue(false);
        }
    }
}
