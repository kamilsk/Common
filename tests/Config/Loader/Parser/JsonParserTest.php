<?php

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
        $parser = new JsonParser(false, 0, 512);
        $json = '{"а":"б"}';
        self::assertEquals(json_decode($json), $parser->parse($json));
    }

    /**
     * @test
     * @dataProvider parserProvider
     *
     * @param ParserInterface $parser
     */
    public function parseSuccess(ParserInterface $parser)
    {
        $content = '{"json": "valid"}';
        $result = $parser->parse($content);
        self::assertArrayHasKey('json', $result);
    }

    /**
     * @test
     * @dataProvider parserProvider
     * @expectedException \Exception
     *
     * @param ParserInterface $parser
     */
    public function parseFail(ParserInterface $parser)
    {
        $content = '{"json": invalid}';
        $parser->parse($content);
    }

    /**
     * @test
     * @dataProvider parserProvider
     *
     * @param ParserInterface $parser
     */
    public function supports(ParserInterface $parser)
    {
        self::assertTrue($parser->supports('json'));
        self::assertFalse($parser->supports('yml'));
    }

    /**
     * @return array<int,JsonParser[]>
     */
    public function parserProvider()
    {
        return [
            [new JsonParser()],
        ];
    }
}
