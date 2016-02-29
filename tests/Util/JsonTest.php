<?php

namespace OctoLab\Common\Util;

/**
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
        self::assertNotEquals(json_encode(['a' => 'б']), $json->encode(['a' => 'б']));
    }

    /**
     * @test
     * @dataProvider jsonProvider
     *
     * @param Json $json
     */
    public function encode(Json $json)
    {
        self::assertJsonStringEqualsJsonString(json_encode([]), $json->encode([]));
    }

    /**
     * @test
     * @dataProvider jsonProvider
     *
     * @param Json $json
     */
    public function decode(Json $json)
    {
        self::assertEquals(json_decode(json_encode([])), $json->decode($json->encode([])));
    }

    /**
     * @test
     * @dataProvider jsonProvider
     * @expectedException \InvalidArgumentException
     *
     * @param Json $json
     */
    public function throwInvalidArgumentException(Json $json)
    {
        $json->encode("\xB1\x31");
    }

    /**
     * @test
     * @dataProvider jsonProvider
     * @expectedException \OverflowException
     *
     * @param Json $json
     */
    public function throwOverflowException(Json $json)
    {
        $json->decode($json->encode(['a' => ['b' => ['c' => false]]]), false, 2);
    }

    /**
     * @test
     * @dataProvider jsonProvider
     * @expectedException \UnexpectedValueException
     *
     * @param Json $json
     */
    public function throwUnexpectedValueException(Json $json)
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
