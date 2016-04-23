<?php

declare(strict_types = 1);

namespace OctoLab\Common\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class ArrayHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function findByPath()
    {
        $scope = ['array' => ['helper' => 'test']];
        self::assertEquals('test', ArrayHelper::findByPath('array:helper', $scope));
        self::assertNull(ArrayHelper::findByPath('array:helper:test', $scope));
        self::assertEquals($scope['array'], ArrayHelper::findByPath('array', $scope));
        self::assertNull(ArrayHelper::findByPath('unknown', $scope));
    }

    /**
     * @test
     * @dataProvider arrayProvider
     *
     * @param array $expected
     * @param array[] ...$arrays
     */
    public function merge(array $expected, array ...$arrays)
    {
        self::assertEquals($expected, ArrayHelper::merge(...$arrays));
    }

    /**
     * @test
     */
    public function transform()
    {
        $target = [
            'app' => [
                'placeholder_parameter' => '%placeholder%',
                'constant' => 'const(E_ALL)',
            ],
        ];
        $expected = [
            'app' => [
                'placeholder_parameter' => 'transformed',
                'constant' => E_ALL,
            ],
        ];
        ArrayHelper::transform($target, ['placeholder' => 'transformed']);
        self::assertEquals($expected, $target);
    }

    /**
     * @return array
     */
    public function arrayProvider(): array
    {
        return [
            [
                [1, 2, 3, 4, 5],
                [1, 2],
                [3, 4, 5],
            ],
            [
                ['a' => 'b', 'c' => 'd', 'e' => 'f'],
                ['a' => 'g', 'c' => 'd'],
                ['a' => 'b', 'e' => 'f'],
            ],
            [
                ['a' => [1, 'b' => 'c', 2, 3, 4, 5]],
                ['a' => [1, 'b' => 'd', 2]],
                ['a' => [3, 4, 'b' => 'c', 5]],
            ],
        ];
    }
}
