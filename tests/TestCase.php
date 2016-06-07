<?php

declare(strict_types = 1);

namespace OctoLab\Common;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return string
     */
    protected function getAppDir(): string
    {
        return sprintf('%s/fixtures/app', __DIR__);
    }

    /**
     * @param string $config
     * @param string $extension
     *
     * @return string
     */
    protected function getConfigPath(string $config = 'config', string $extension = 'yml'): string
    {
        return sprintf('%s/fixtures/config/%s.%s', __DIR__, $config, $extension);
    }

    /**
     * @return string
     */
    protected function getMigrationDir(): string
    {
        return sprintf('%s/fixtures/migrations', __DIR__);
    }

    /**
     * @param string $migration
     *
     * @return string
     */
    protected function getMigrationPath(string $migration): string
    {
        return sprintf('%s/%s', $this->getMigrationDir(), $migration);
    }
}
