<?php

declare(strict_types = 1);

namespace OctoLab\Common\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class System
{
    /**
     * @return bool
     *
     * @api
     */
    public static function isLinux(): bool
    {
        return stripos(PHP_OS, 'LIN') === 0;
    }

    /**
     * @return bool
     *
     * @api
     */
    public static function isMac(): bool
    {
        return stripos(PHP_OS, 'DAR') === 0;
    }

    /**
     * @return bool
     *
     * @api
     */
    public static function isWindows(): bool
    {
        return stripos(PHP_OS, 'WIN') === 0;
    }
}
