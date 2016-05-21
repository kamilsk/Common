<?php

declare(strict_types = 1);

namespace OctoLab\Common;

if (!function_exists('OctoLab\Common\camelize')) {
    /**
     * @param string $word
     *
     * @return string
     */
    function camelize(string $word): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $word)));
    }
}
