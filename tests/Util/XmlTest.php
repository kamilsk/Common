<?php

declare(strict_types = 1);

namespace OctoLab\Common\Util;

use OctoLab\Common\Exception\XmlException;
use OctoLab\Common\TestCase;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class XmlTest extends TestCase
{
    /**
     * @test
     */
    public function new()
    {
        $xml = '<test>xml</test>';
        self::assertEquals(
            simplexml_load_string($xml, \SimpleXMLElement::class),
            Xml::new(\SimpleXMLElement::class)->parse($xml)
        );
    }

    /**
     * @test
     */
    public function parse()
    {
        $xml = '<test>xml</test>';
        self::assertEquals(simplexml_load_string($xml), Xml::new()->parse($xml));
    }

    /**
     * @test
     */
    public function parseFailure()
    {
        $xml = '<test>xml</invalid>';
        try {
            Xml::new()->parse($xml);
            self::fail(sprintf('%s exception expected.', XmlException::class));
        } catch (XmlException $e) {
            self::assertEquals(sprintf("Invalid xml string \n\n%s\n", $xml), $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function softParse()
    {
        $xml = '<test>xml</test>';
        list($content,) = Xml::new()->softParse($xml);
        self::assertEquals(simplexml_load_string($xml), $content);
    }

    /**
     * @test
     */
    public function softParseFailure()
    {
        $xml = '<test>xml</invalid>';
        list(, $error) = Xml::new()->softParse($xml);
        self::assertInstanceOf(XmlException::class, $error);
    }
}
