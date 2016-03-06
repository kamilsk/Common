<?php

namespace OctoLab\Common\Config\Loader\Parser;

use Symfony\Component\Yaml\Parser;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class YamlParser implements ParserInterface
{
    /** @var Parser */
    private $decoder;
    /** @var bool */
    private $exceptionOnInvalidType;
    /** @var bool */
    private $objectForMap;
    /** @var bool */
    private $objectSupport;

    /**
     * @param bool $exceptionOnInvalidType
     * @param bool $objectSupport
     * @param bool $objectForMap
     *
     * @see \Symfony\Component\Yaml\Parser::parse
     *
     * @api
     */
    public function __construct($exceptionOnInvalidType = false, $objectSupport = false, $objectForMap = false)
    {
        $this->decoder = new Parser();
        $this->exceptionOnInvalidType = $exceptionOnInvalidType;
        $this->objectForMap = $objectForMap;
        $this->objectSupport = $objectSupport;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($content)
    {
        return $this->decoder->parse(
            $content,
            $this->exceptionOnInvalidType,
            $this->objectSupport,
            $this->objectForMap
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supports($extension)
    {
        return in_array(strtolower($extension), ['yml', 'yaml'], true);
    }
}
