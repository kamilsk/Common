<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class Dumper
{
    /**
     * @param mixed $value
     *
     * @return string
     *
     * @api
     */
    public static function dumpToString($value): string
    {
        $string = (string)print_r($value, true);
        while (preg_match('/\n|\s{2}/', $string)) {
            $string = preg_replace('/\n\s*/', '', $string);
            $string = preg_replace('/\s{2,}/', ' ', $string);
        }
        $string = preg_replace('/(\w{1})\[/', '$1,[', $string);
        return $string;
    }
}
