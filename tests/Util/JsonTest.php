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
    public function decode()
    {
        $json = Json::new();
        self::assertEquals(json_decode(json_encode([])), $json->decode($json->encode([])));
    }

    /**
     * @test
     */
    public function decodeFailure()
    {
        $json = Json::new();
        try {
            $json->decode($json->encode(['a' => ['b' => ['c' => false]]]), false, 2);
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
            self::assertEquals(JSON_ERROR_DEPTH, $e->getCode());
        }
        // JSON_ERROR_STATE_MISMATCH
        try {
            $json->decode('{"test": "success"}' . "\u{0000}");
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
            self::assertEquals(JSON_ERROR_CTRL_CHAR, $e->getCode());
        }
        try {
            $json->decode('{"invalid"}');
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
            self::assertEquals(JSON_ERROR_SYNTAX, $e->getCode());
        }
    }

    /**
     * @test
     */
    public function encode()
    {
        self::assertJsonStringEqualsJsonString(json_encode([]), Json::new()->encode([]));
    }

    /**
     * @test
     */
    public function encodeFailure()
    {
        $json = Json::new();
        try {
            $json->encode("\xB1\x31");
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
            self::assertEquals(JSON_ERROR_UTF8, $e->getCode());
        }
        try {
            $a = new \stdClass();
            $b = new \stdClass();
            $a->link = $b;
            $b->link = $a;
            $json->encode($a);
        } catch (\InvalidArgumentException $e) {
            self::assertEquals(JSON_ERROR_RECURSION, $e->getCode());
        }
        try {
            $json->encode(NAN);
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
            self::assertEquals(JSON_ERROR_INF_OR_NAN, $e->getCode());
        }
        try {
            $json->encode(curl_init());
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
            self::assertEquals(JSON_ERROR_UNSUPPORTED_TYPE, $e->getCode());
        }
    }

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
    public function softDecode()
    {
        $json = json_encode([]);
        list($content,) = Json::new()->softDecode($json);
        self::assertEquals(json_decode($json), $content);
    }

    /**
     * @test
     */
    public function softDecodeFailure()
    {
        list(, $error) = Json::new()->softDecode('{"invalid"}');
        self::assertInstanceOf(\InvalidArgumentException::class, $error);
    }

    /**
     * @test
     */
    public function softEncode()
    {
        list($json,) = Json::new()->softEncode([]);
        self::assertJsonStringEqualsJsonString(json_encode([]), $json);
    }

    /**
     * @test
     */
    public function softEncodeFailure()
    {
        list(, $error) = Json::new()->softEncode("\xB1\x31");
        self::assertInstanceOf(\InvalidArgumentException::class, $error);
    }
}
