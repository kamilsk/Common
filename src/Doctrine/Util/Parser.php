<?php

declare(strict_types = 1);

namespace OctoLab\Common\Doctrine\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class Parser
{
    /**
     * @param string $text
     *
     * @return string[] sql instructions
     *
     * @api
     */
    public static function extractSql(string $text): array
    {
        // remove comments
        // inline
        $text = (string)preg_replace('/\s*(?:--|#).*$/um', '', $text);
        // multi-line
        $text = (string)preg_replace('/\/\*[^*]*(\*)?[^*]*\*\//um', '', $text);
        // flatten and filter
        $text = (string)preg_replace('/\n/', ' ', $text);
        $text = (string)preg_replace('/\s{2,}/', ' ', $text);
        $text = trim($text, '; ');
        return $text === '' ? [] : preg_split('/\s*;\s*/', $text);
    }
}
