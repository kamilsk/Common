<?php

namespace OctoLab\Common\Util;

use OctoLab\Common\TestCase;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class SystemTest extends TestCase
{
    /**
     * @test
     */
    public function assertion()
    {
        self::assertTrue(System::isLinux() || System::isMac() || System::isWindows());
        if (System::isLinux()) {
            self::assertFalse(System::isMac());
            self::assertFalse(System::isWindows());
        } elseif (System::isMac()) {
            self::assertFalse(System::isWindows());
        }
    }
}
