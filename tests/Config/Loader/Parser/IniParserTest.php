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
    public function parseFailure()
    {
        $ini = '{ini=invalid}';
        try {
            $this->getParser()->parse($ini);
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
            self::assertEquals(sprintf("Invalid ini string \n\n%s\n", $ini), $e->getMessage());
        }
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
