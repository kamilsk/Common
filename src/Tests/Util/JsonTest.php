<?php

namespace OctoLab\Common\Tests\Util;

use OctoLab\Common\Util\Json;

/**
 * phpunit src/Tests/Util/JsonTest.php
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class JsonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function construct()
    {
        $json = new Json(true, JSON_UNESCAPED_UNICODE, 2);
        $value = json_encode(['a' => 'b']);
        self::assertNotEquals(json_decode($value), $json->decode($value));
    }

    /**
     * @test
     * @dataProvider jsonProvider
     *
     * @param Json $json
     */
    public function encode(Json $json)
    {
        $value = [];
        self::assertJsonStringEqualsJsonString(json_encode($value), $json->encode($value));
    }

    /**
     * @test
     * @dataProvider jsonProvider
     *
     * @param Json $json
     */
    public function decode(Json $json)
    {
        $value = json_encode([]);
        self::assertEquals(json_decode($value), $json->decode($value));
    }

    /**
     * @test
     * @dataProvider jsonProvider
     * @expectedException \InvalidArgumentException
     *
     * @param Json $json
     */
    public function handleInvalidArgumentException(Json $json)
    {
        $value = "\xB1\x31";
        $json->encode($value);
    }

    /**
     * @test
     * @dataProvider jsonProvider
     * @expectedException \OverflowException
     *
     * @param Json $json
     */
    public function handleOverflowException(Json $json)
    {
        $value = '{"a":{"b":{"c":false}}}';
        $json->decode($value, false, 2);
    }

    /**
     * @test
     * @dataProvider jsonProvider
     * @expectedException \UnexpectedValueException
     *
     * @param Json $json
     */
    public function handleUnexpectedValueException(Json $json)
    {
        $reflection = new \ReflectionObject($json);
        $method = $reflection->getMethod('getException');
        $method->setAccessible(true);
        $json->encode([]);
        throw $method->invoke($json);
    }

    /**
     * @return array[]
     */
    public function jsonProvider()
    {
        return [
            [new Json()],
        ];
    }
}
