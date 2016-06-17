<?php declare(strict_types = 1);

namespace OctoLab\Common\Composer\Script\Asset;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
interface ConfigInterface extends \ArrayAccess
{
    /**
     * @return string
     *
     * @api
     */
    public function getTargetPath(): string;

    /**
     * @return bool
     *
     * @api
     */
    public function isSymlink(): bool;

    /**
     * @return bool
     *
     * @api
     */
    public function isRelative(): bool;
}
