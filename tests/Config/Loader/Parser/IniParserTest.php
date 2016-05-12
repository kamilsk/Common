<?php

declare(strict_types = 1);

namespace OctoLab\Common\Config\Loader\Parser;

use OctoLab\Common\TestCase;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class IniParserTest extends TestCase
{
    /**
     * @test
     */
    public function construct()
    {
        $ini = 'ini=valid';
        self::assertEquals(parse_ini_string($ini), (new IniParser(false))->parse('ini=valid'));
    }

    /**
     * @test
     */
    public function parseSuccess()
    {
        self::assertArrayHasKey('ini', $this->getParser()->parse('ini=valid'));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function parseFailure()
    {
        $this->getParser()->parse('{ini=invalid}');
    }

    /**
     * @test
     */
    public function supports()
    {
        $parser = $this->getParser();
        self::assertTrue($parser->supports('ini'));
        self::assertFalse($parser->supports('yml'));
    }

    /**
     * @return IniParser
     */
    private function getParser(): IniParser
    {
        return new IniParser();
    }
}
