<?php

declare(strict_types = 1);

namespace OctoLab\Common\Composer\Script;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class PublisherConfig extends \ArrayObject
{
    /**
     * @param string $name
     * @param array $input
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $name, array $input)
    {
        if (!isset($input['target'])) {
            throw new \InvalidArgumentException(sprintf('The extra.%s must contains target path.', $name));
        }
        parent::__construct($input + ['symlink' => false, 'relative' => false]);
    }

    /**
     * @return string
     */
    public function getTargetPath(): string
    {
        return $this->offsetGet('target');
    }

    /**
     * @return bool
     */
    public function isSymlink(): bool
    {
        return $this->offsetGet('symlink');
    }

    /**
     * @return bool
     */
    public function isRelative(): bool
    {
        return $this->offsetGet('relative');
    }
}
