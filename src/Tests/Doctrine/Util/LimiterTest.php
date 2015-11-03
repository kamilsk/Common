<?php

namespace OctoLab\Common\Tests\Helper;

use OctoLab\Common\Doctrine\Util\Limiter;

/**
 * phpunit src/Tests/Doctrine/Util/LimiterTest.php
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class LimiterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function getLimit()
    {
        $limiter = new Limiter(200, 20, 100);
        self::assertEquals(100, $limiter->getLimit());
        $limiter = new Limiter(100, 20, 200);
        self::assertEquals(100, $limiter->getLimit());
    }

    /**
     * @test
     */
    public function hasPortion()
    {
        $limiter = new Limiter(100, 20, 200);
        $i = 0;
        do {
            $i++;
        } while ($limiter->nextPortion()->hasPortion());
        self::assertEquals(2, $i);
        self::assertEquals(0, $limiter->getLimit());
        self::assertEquals(200, $limiter->getOffset());
    }

    /**
     * @test
     */
    public function nextPortion()
    {
        $limiter = new Limiter(100, 20, 200);
        $limiter->nextPortion();
        self::assertEquals(80, $limiter->getLimit());
        self::assertEquals(120, $limiter->getOffset());
        $limiter = new Limiter(200, 20, 100);
        $limiter->nextPortion();
        self::assertEquals(0, $limiter->getLimit());
        self::assertEquals(100, $limiter->getOffset());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function throwInvalidArgumentException()
    {
        new Limiter(100, -100);
    }
}
