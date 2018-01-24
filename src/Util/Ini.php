<?php

declare(strict_types = 1);

namespace OctoLab\Common\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class Ini
{
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
        \assert('$scannerMode >= 0');
        $this->processSections = $processSection;
        $this->scannerMode = $scannerMode;
    }

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

    /**
     * @param string $ini
     * @param bool|null $processSections
     * @param int|null $scannerMode
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     *
     * @api
     */
    public function parse(string $ini, bool $processSections = null, int $scannerMode = null): array
    {
        list($content, $error) = $this->softParse($ini, $processSections, $scannerMode);
        if ($error !== null) {
            throw $error;
        }
        return $content;
    }

    /**
     * @param string $ini
     * @param bool|null $processSections
     * @param int|null $scannerMode
     *
     * @return array where first element is a result of parse_ini_string, second - \InvalidArgumentException or null
     *
     * @api
     */
    public function softParse(string $ini, bool $processSections = null, int $scannerMode = null): array
    {
        \assert('$scannerMode === null || $scannerMode >= 0');
        error_reporting(($before = error_reporting()) & ~E_WARNING);
        $content = parse_ini_string(
            $ini,
            $processSections ?? $this->processSections,
            $scannerMode ?? $this->scannerMode
        );
        error_reporting($before);
        if ($content === false) {
            return [null, new \InvalidArgumentException(sprintf("Invalid ini string \n\n%s\n", $ini))];
        }
        return [$content, null];
    }
}
