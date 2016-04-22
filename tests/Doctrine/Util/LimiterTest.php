<?php

declare(strict_types = 1);

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
     * @param int $table1Count
     * @param int $table2Count
     * @param int $limit
     * @param int $offset
     * @param array $expected
     */
    public function twoTablePagination(int $table1Count, int $table2Count, int $limit, int $offset, array $expected)
    {
        self::assertEquals($expected, Limiter::getTwoTablePagination($table1Count, $table2Count, $limit, $offset));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function constructFailure()
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
        $limiter = new Limiter(100, 20);
        self::assertEquals(100, $limiter->getLimit());
    }

    /**
     * @test
     */
    public function getOffset()
    {
        $limiter = new Limiter(100);
        self::assertEquals(0, $limiter->getOffset());
        $limiter = new Limiter(100, 100);
        self::assertEquals(100, $limiter->getOffset());
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
     * @return array
     */
    public function tableDataProvider(): array
    {
        return [
            [
                10, 100, 2, 6,
                [
                    'limit1' => 2,
                    'offset1' => 6,
                    'limit2' => 0,
                    'offset2' => 0,
                ],
            ],
            [
                10, 100, 6, 6,
                [
                    'limit1' => 4,
                    'offset1' => 6,
                    'limit2' => 2,
                    'offset2' => 0,
                ],
            ],
            [
                10, 100, 110, 6,
                [
                    'limit1' => 4,
                    'offset1' => 6,
                    'limit2' => 100,
                    'offset2' => 0,
                ],
            ],
            [
                10, 100, 10, 12,
                [
                    'limit1' => 0,
                    'offset1' => 10,
                    'limit2' => 10,
                    'offset2' => 2,
                ],
            ],
            [
                10, 100, 110, 12,
                [
                    'limit1' => 0,
                    'offset1' => 10,
                    'limit2' => 98,
                    'offset2' => 2,
                ],
            ],
            [
                10, 100, 10, 110,
                [
                    'limit1' => 0,
                    'offset1' => 10,
                    'limit2' => 0,
                    'offset2' => 100,
                ],
            ],
            [
                0, 0, 10, 10,
                [
                    'limit1' => 0,
                    'offset1' => 0,
                    'limit2' => 0,
                    'offset2' => 0,
                ],
            ],
            [
                10, 100, 10, 0,
                [
                    'limit1' => 10,
                    'offset1' => 0,
                    'limit2' => 0,
                    'offset2' => 0,
                ],
            ],
            [
                10, 100, 0, 10,
                [
                    'limit1' => 0,
                    'offset1' => 10,
                    'limit2' => 0,
                    'offset2' => 0,
                ],
            ],
            [
                0, 100, 10, 90,
                [
                    'limit1' => 0,
                    'offset1' => 0,
                    'limit2' => 10,
                    'offset2' => 90,
                ],
            ],
            [
                10, 0, 10, 10,
                [
                    'limit1' => 0,
                    'offset1' => 10,
                    'limit2' => 0,
                    'offset2' => 0,
                ],
            ],
        ];
    }
}
