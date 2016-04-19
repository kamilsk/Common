<?php

declare(strict_types = 1);

namespace OctoLab\Common\Monolog\Handler;

use Monolog\Handler\StreamHandler;

/**
 * Reset stream file descriptor after deleting it when logger is still running.
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class BulletproofStreamHandler extends StreamHandler
{
    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        if ($this->url && !file_exists($this->url)) {
            $this->stream = null;
        }
        parent::write($record);
    }
}
