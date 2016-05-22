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
    public function cases()
    {
        $exceptionGenerator = function (int $code) {
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
                    echo 'Caught!';
                })
                ->end(2)
            ;
            self::assertContains('Caught!', ob_get_clean());
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

    /**
     * @test
     */
    public function end()
    {
        $sugar = Call::begin(function (string $message, int $code = 0) {
            throw new \RuntimeException($message, $code);
        });
        try {
            $sugar->end('Exception is not suppressed.');
            self::assertTrue(false);
        } catch (\RuntimeException $e) {
            self::assertTrue(true);
        }
    }

    /**
     * @test
     */
    public function go()
    {
        list($result, $err) = Call::go(function () {
            throw new \Exception();
        });
        self::assertNull($result);
        self::assertInstanceOf(\Exception::class, $err);
        list($result, $err) = Call::go(function () {
            return 1;
        });
        self::assertEquals(1, $result);
        self::assertNull($err);
    }

    /**
     * @test
     */
    public function rescue()
    {
        $callable = Call::begin(function (string $message, int $code = 0) {
            throw new \Exception($message, $code);
        });
        try {
            $callable->rescue()->end('Exception is suppressed!');
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
        $callable = Call::begin(function (string $message, int $code = 0) use (&$times) {
            $times++;
            throw new \Exception($message, $code);
        });
        try {
            $callable->rescue()->retry(3)->end('Exception is suppressed three times!');
            self::assertEquals(3, $times);
        } catch (\Exception $e) {
            self::assertTrue(false);
        }
    }

    /**
     * @test
     */
    public function issue68and69()
    {
        $callable = Call::begin(function () {
            throw new \RuntimeException();
        })->rescue(
            \Exception::class,
            function () : string {
                return 'success';
            },
            true
        );
        self::assertEquals('success', $callable());
    }
}
