<?php

declare(strict_types = 1);

namespace OctoLab\Common\Composer\Script\AdminLte;

use OctoLab\Common\Composer\Script\ConfigInterface;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class Config extends \ArrayObject implements ConfigInterface
{
    /**
     * @param array $extra
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $extra)
    {
        if (!isset($extra['target'])) {
            throw new \InvalidArgumentException('The extra.admin-lte must contains target path.');
        }
        parent::__construct($extra + ['symlink' => false, 'relative' => false]);
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetPath(): string
    {
        return $this->offsetGet('target');
    }

    /**
     * @return bool
     *
     * @api
     */
    public function isBootstrapEnabled(): bool
    {
        return $this->offsetExists('bootstrap') && $this->offsetGet('bootstrap');
    }

    /**
     * @return bool
     *
     * @api
     */
    public function isDemoEnabled(): bool
    {
        return $this->offsetExists('demo') && $this->offsetGet('demo');
    }

    /**
     * @return bool
     *
     * @api
     */
    public function isPluginsEnabled(): bool
    {
        return $this->offsetExists('plugins') && $this->offsetGet('plugins');
    }

    /**
     * {@inheritdoc}
     */
    public function isRelative(): bool
    {
        return $this->offsetGet('relative');
    }

    /**
     * {@inheritdoc}
     */
    public function isSymlink(): bool
    {
        return $this->offsetGet('symlink');
    }
}
