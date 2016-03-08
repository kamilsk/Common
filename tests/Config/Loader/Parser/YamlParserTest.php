<?php

namespace OctoLab\Common\Config\Loader\Parser;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class YamlParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function construct()
    {
        $parser = new YamlParser(true, true, true);
        $expected = new \stdClass();
        $expected->yaml = 'valid';
        self::assertEquals($expected, $parser->parse('yaml: valid'));
    }

    /**
     * @test
     * @dataProvider parserProvider
     *
     * @param ParserInterface $parser
     */
    public function parseSuccess(ParserInterface $parser)
    {
        $result = $parser->parse('yaml: valid');
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
        $parser->parse('yaml: { invalid }: true');
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
     * @return array<int,YamlParser[]>
     */
    public function parserProvider()
    {
        return [
            [new YamlParser()],
        ];
    }
}
