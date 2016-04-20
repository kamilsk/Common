<?php

declare(strict_types = 1);

namespace OctoLab\Common\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class JsonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function new()
    {
        $json = Json::new(true, JSON_UNESCAPED_UNICODE);
        self::assertNotEquals(json_encode(['a' => 'б']), $json->encode(['a' => 'б']));
    }

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
    public function decodeFailure(Json $json)
    {
        try {
            $json->decode($json->encode(['a' => ['b' => ['c' => false]]]), false, 2);
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
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
    public function encodeFailure(Json $json)
    {
        try {
            $json->encode("\xB1\x31");
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
            self::assertTrue(true);
        }
        try {
            $json->encode(curl_init());
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
            self::assertTrue(true);
        }
        try {
            $json->encode(NAN);
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
            self::assertTrue(true);
        }
    }

    /**
     * @return array<int,Json[]>
     */
    public function jsonProvider()
    {
        return [
            [new Json()],
        ];
    }
}
