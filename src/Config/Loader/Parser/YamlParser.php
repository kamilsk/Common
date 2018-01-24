<?php

declare(strict_types = 1);

namespace OctoLab\Common\Config\Loader\Parser;

use Symfony\Component\Yaml\Parser;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class YamlParser implements ParserInterface
{
    /** @var bool */
    private $exceptionOnInvalidType;
    /** @var bool */
    private $objectForMap;
    /** @var bool */
    private $objectSupport;
    /** @var Parser */
    private $serializer;

    /**
     * @param bool $exceptionOnInvalidType
     * @param bool $objectSupport
     * @param bool $objectForMap
     *
     * @api
     */
    public function __construct(
        bool $exceptionOnInvalidType = false,
        bool $objectSupport = false,
        bool $objectForMap = false
    ) {
        $this->serializer = new Parser();
        $this->exceptionOnInvalidType = $exceptionOnInvalidType;
        $this->objectForMap = $objectForMap;
        $this->objectSupport = $objectSupport;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(string $content)
    {
        return $this->serializer->parse(
            $content,
            $this->exceptionOnInvalidType,
            $this->objectSupport,
            $this->objectForMap
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $extension): bool
    {
        return \in_array(strtolower($extension), ['yml', 'yaml'], true);
    }
}
