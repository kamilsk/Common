<?php

declare(strict_types = 1);

namespace OctoLab\Common\Config\Loader\Parser;

use OctoLab\Common\Util\Ini;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class IniParser implements ParserInterface
{
    /** @var Ini */
    private $decoder;

    /**
     * @param bool $processSections
     * @param int $scannerMode
     */
    public function __construct(bool $processSections = true, int $scannerMode = INI_SCANNER_NORMAL)
    {
        $this->decoder = new Ini($processSections, $scannerMode);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function parse(string $content)
    {
        return $this->decoder->parse($content);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $extension): bool
    {
        return 'ini' === strtolower($extension);
    }
}
