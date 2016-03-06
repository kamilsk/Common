<?php

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
        self::assertEquals('test', ArrayHelper::findByPath($scope, 'array:helper'));
        self::assertNull(ArrayHelper::findByPath($scope, 'array:helper:test'));
        self::assertEquals($scope['array'], ArrayHelper::findByPath($scope, 'array'));
        self::assertNull(ArrayHelper::findByPath($scope, 'unknown'));
    }

    /**
     * @test
     * @dataProvider arrayProvider
     *
     * @param array $a
     * @param array $b
     * @param array $expected
     */
    public function merge(array $a, array $b, array $expected)
    {
        self::assertEquals(ArrayHelper::merge($a, $b), $expected);
    }

    /**
     * @test
     */
    public function transform()
    {
        $target = [
            'component' => [
                'parameter' => 'base component\'s parameter will be overwritten by root config',
                'placeholder_parameter' => '%placeholder%',
                'constant' => 'const(E_ALL)',
            ],
        ];
        $expected = [
            'component' => [
                'parameter' => 'base component\'s parameter will be overwritten by root config',
                'placeholder_parameter' => 'transformed',
                'constant' => E_ALL,
            ],
        ];
        ArrayHelper::transform($target, ['placeholder' => 'transformed']);
        self::assertEquals($expected, $target);
    }

    /**
     * @return array<int, array[]>
     */
    public function arrayProvider()
    {
        return [
            [
                [1, 2],
                [3, 4, 5],
                [1, 2, 3, 4, 5],
            ],
            [
                ['a' => 'g', 'c' => 'd'],
                ['a' => 'b', 'e' => 'f'],
                ['a' => 'b', 'c' => 'd', 'e' => 'f'],
            ],
            [
                ['a' => [1, 'b' => 'd', 2]],
                ['a' => [3, 4, 'b' => 'c', 5]],
                ['a' => [1, 'b' => 'c', 2, 3, 4, 5]],
            ],
        ];
    }
}
