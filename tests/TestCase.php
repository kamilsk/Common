<?php

declare(strict_types = 1);

namespace OctoLab\Common;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $config
     * @param string $extension
     *
     * @return string
     */
    protected function getConfigPath(string $config = 'config', string $extension = 'yml'): string
    {
        return sprintf('%s/app/config/%s.%s', __DIR__, $config, $extension);
    }

    /**
     * @return string
     */
    protected function getAppDir(): string
    {
        return realpath(__DIR__ . '/app');
    }
}
