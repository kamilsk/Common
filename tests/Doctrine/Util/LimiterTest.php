<?php

namespace OctoLab\Common\Doctrine\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class LimiterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider tableDataProvider
     *
     * @param int $tableCount1
     * @param int $tableCount2
     * @param int $limit
     * @param int $offset
     * @param array $expected
     */
    public function twoTablePagination($tableCount1, $tableCount2, $limit, $offset, array $expected)
    {
        $actual = Limiter::getTwoTablePagination($tableCount1, $tableCount2, $limit, $offset);
        self::assertEquals($expected, $actual);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function constructFail()
    {
        new Limiter(100, -100);
    }

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
     * @return array[]
     */
    public function tableDataProvider()
    {
        return [
            [
                10,
                100,
                2,
                6,
                [
                    'limit1' => 2,
                    'limit2' => 0,
                    'offset1' => 6,
                    'offset2' => 0,
                ],
            ],
            [
                10,
                100,
                4,
                8,
                [
                    'limit1' => 2,
                    'limit2' => 2,
                    'offset1' => 8,
                    'offset2' => 0,
                ],
            ],
            [
                10,
                100,
                2,
                12,
                [
                    'limit1' => 0,
                    'limit2' => 2,
                    'offset1' => 10,
                    'offset2' => 2,
                ],
            ],
            [
                10,
                100,
                10,
                110,
                [
                    'limit1' => 0,
                    'limit2' => 0,
                    'offset1' => 10,
                    'offset2' => 100,
                ],
            ],
            [
                0,
                0,
                10,
                10,
                [
                    'limit1' => 0,
                    'limit2' => 0,
                    'offset1' => 0,
                    'offset2' => 0,
                ],
            ],
            [
                10,
                100,
                10,
                0,
                [
                    'limit1' => 10,
                    'limit2' => 0,
                    'offset1' => 0,
                    'offset2' => 0,
                ],
            ],
            [
                0,
                100,
                10,
                90,
                [
                    'limit1' => 0,
                    'limit2' => 10,
                    'offset1' => 0,
                    'offset2' => 90,
                ],
            ],
            [
                10,
                0,
                10,
                10,
                [
                    'limit1' => 0,
                    'limit2' => 0,
                    'offset1' => 10,
                    'offset2' => 0,
                ],
            ],
        ];
    }
}
