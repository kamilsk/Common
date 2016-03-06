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
        $json = new Json(true, JSON_UNESCAPED_UNICODE);
        self::assertNotEquals(json_encode(['a' => 'б']), $json->encode(['a' => 'б']));
    }

    /**
     * @test
     * @dataProvider jsonProvider
     *
     * @param Json $json
     */
    public function decodeSuccess(Json $json)
    {
        self::assertEquals(json_decode(json_encode([])), $json->decode($json->encode([])));
    }

    /**
     * @test
     * @dataProvider jsonProvider
     *
     * @param Json $json
     */
    public function decodeFail(Json $json)
    {
        try {
            $json->decode($json->encode(['a' => ['b' => ['c' => false]]]), false, 2);
            self::assertTrue(false);
        } catch (\OverflowException $e) {
            self::assertTrue(true);
        }
    }

    /**
     * @test
     * @dataProvider jsonProvider
     *
     * @param Json $json
     */
    public function encodeSuccess(Json $json)
    {
        self::assertJsonStringEqualsJsonString(json_encode([]), $json->encode([]));
    }

    /**
     * @test
     * @dataProvider jsonProvider
     *
     * @param Json $json
     */
    public function encodeFail(Json $json)
    {
        try {
            $json->encode("\xB1\x31");
            self::assertTrue(false);
        } catch (\InvalidArgumentException $e) {
            self::assertTrue(true);
        }
        $reflection = new \ReflectionObject($json);
        $method = $reflection->getMethod('getException');
        $method->setAccessible(true);
        $json->encode([]);
        $exception = $method->invoke($json);
        self::assertInstanceOf(\UnexpectedValueException::class, $exception);
    }

    /**
     * @return array<int, Json[]>
     */
    public function jsonProvider()
    {
        return [
            [new Json()],
        ];
    }
}
