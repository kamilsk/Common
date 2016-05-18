<?php

declare(strict_types = 1);

namespace OctoLab\Common\Config\Loader\Parser;

use OctoLab\Common\Util\Ini;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class IniParser implements ParserInterface
{
    /** @var Ini */
    private $serializer;

    /**
     * @param bool $processSections
     * @param int $scannerMode
     *
     * @api
     */
    public function __construct(bool $processSections = true, int $scannerMode = INI_SCANNER_NORMAL)
    {
        $this->serializer = new Ini($processSections, $scannerMode);
    }

    /**
     * {@inheritdoc}
     */
    public function parse(string $content)
    {
        return $this->serializer->parse($content);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $extension): bool
    {
        return 'ini' === strtolower($extension);
    }
}
