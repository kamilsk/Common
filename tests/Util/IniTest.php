<?php

declare(strict_types = 1);

namespace OctoLab\Common\Util;

use OctoLab\Common\TestCase;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class IniTest extends TestCase
{
    /**
     * @test
     */
    public function new()
    {
        $ini = 'ini=valid';
        self::assertEquals(parse_ini_string($ini), Ini::new()->parse($ini));
    }

    /**
     * @test
     */
    public function parseFailure()
    {
        $ini = '{ini=invalid}';
        try {
            Ini::new()->parse($ini);
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
            self::assertEquals(sprintf("Invalid ini string \n\n%s\n", $ini), $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function parseSuccess()
    {
        self::assertEquals(['ini' => 'valid'], Ini::new()->parse('ini=valid'));
    }
}
