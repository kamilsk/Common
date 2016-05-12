<?php

declare(strict_types = 1);

namespace OctoLab\Common\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class Ini
{
    /**
     * @param bool $processSections
     * @param int $scannerMode
     *
     * @return Ini
     *
     * @api
     */
    public static function new(bool $processSections = false, int $scannerMode = INI_SCANNER_NORMAL): Ini
    {
        return new self($processSections, $scannerMode);
    }

    /** @var bool */
    private $processSections;
    /** @var int */
    private $scannerMode;

    /**
     * @param bool $processSection
     * @param int $scannerMode
     *
     * @api
     */
    public function __construct(bool $processSection, int $scannerMode)
    {
        $this->processSections = $processSection;
        $this->scannerMode = $scannerMode;
    }

    /**
     * @param string $ini
     * @param bool $processSections
     * @param int $scannerMode
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     *
     * @api
     */
    public function parse(string $ini, bool $processSections = null, int $scannerMode = null): array
    {
        $content = @parse_ini_string(
            $ini,
            $processSections ?? $this->processSections,
            $scannerMode ?? $this->scannerMode
        );
        if (false === $content) {
            throw new \InvalidArgumentException(sprintf("Invalid ini string \n\n%s\n", $ini));
        }
        return $content;
    }
}
