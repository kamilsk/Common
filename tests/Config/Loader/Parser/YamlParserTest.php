<?php

declare(strict_types = 1);

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
        self::assertEquals((object)['yaml' => 'valid'], (new YamlParser(true, true, true))->parse('yaml: valid'));
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function parseFailure()
    {
        $this->getParser()->parse('yaml: { invalid }: true');
    }

    /**
     * @test
     */
    public function parseSuccess()
    {
        self::assertArrayHasKey('yaml', $this->getParser()->parse('yaml: valid'));
    }

    /**
     * @test
     */
    public function supports()
    {
        $parser = $this->getParser();
        self::assertTrue($parser->supports('yml'));
        self::assertFalse($parser->supports('json'));
    }

    /**
     * @return YamlParser
     */
    private function getParser(): YamlParser
    {
        return new YamlParser();
    }
}
