<?php

declare(strict_types = 1);

namespace OctoLab\Common\Config\Loader\Parser;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class JsonParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function construct()
    {
        $json = '{"а":"б"}';
        self::assertEquals(json_decode($json), (new JsonParser(false, 0, 512))->parse($json));
    }

    /**
     * @test
     */
    public function parseSuccess()
    {
        self::assertArrayHasKey('json', $this->getParser()->parse('{"json": "valid"}'));
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function parseFailure()
    {
        $this->getParser()->parse('{"json": invalid}');
    }

    /**
     * @test
     */
    public function supports()
    {
        $parser = $this->getParser();
        self::assertTrue($parser->supports('json'));
        self::assertFalse($parser->supports('yml'));
    }

    /**
     * @return JsonParser
     */
    private function getParser(): JsonParser
    {
        return new JsonParser();
    }
}
