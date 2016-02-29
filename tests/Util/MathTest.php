<?php

namespace OctoLab\Common\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class MathTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider twoTablePaginationDataProvider
     *
     * @param int $tableCount1
     * @param int $tableCount2
     * @param int $limit
     * @param int $offset
     * @param array $expected
     */
    public function twoTablePagination($tableCount1, $tableCount2, $limit, $offset, array $expected)
    {
        $helper = new Math();
        $actual = $helper->getTwoTablePagination($tableCount1, $tableCount2, $limit, $offset);
        self::assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function twoTablePaginationDataProvider()
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
