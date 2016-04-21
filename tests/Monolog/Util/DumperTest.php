<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class DumperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider valueProvider
     *
     * @param mixed $value
     * @param string $expected
     */
    public function dumpToString($value, string $expected)
    {
        self::assertEquals($expected, Dumper::dumpToString($value));
    }

    /**
     * @return array[]
     */
    public function valueProvider(): array
    {
        $object = new \stdClass();
        $object->property = 'value';
        $object->another = 'value';
        return [
            [null, ''],
            [true, '1'],
            [1, '1'],
            [1.1, '1.1'],
            ['a', 'a'],
            [(array)$object, 'Array([property] => value,[another] => value)'],
            [$object, 'stdClass Object([property] => value,[another] => value)'],
            [new Dumper(), sprintf('%s Object()', Dumper::class)],
        ];
    }
}
