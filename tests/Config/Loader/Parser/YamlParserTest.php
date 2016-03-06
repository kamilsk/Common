<?php

namespace OctoLab\Common\Config\Loader\Parser;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class YamlParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider parserProvider
     *
     * @param ParserInterface $parser
     */
    public function parseSuccess(ParserInterface $parser)
    {
        $content = 'yaml: valid';
        $result = $parser->parse($content);
        self::assertArrayHasKey('yaml', $result);
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
        $content = 'yaml: invalid: true';
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
        self::assertTrue($parser->supports('yml'));
        self::assertFalse($parser->supports('json'));
    }

    /**
     * @return array<int, YamlParser[]>
     */
    public function parserProvider()
    {
        return [
            [new YamlParser()],
        ];
    }
}
