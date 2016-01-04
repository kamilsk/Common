<?php

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
     * @inheritDoc
     *
     * @api
     */
    protected function write(array $record)
    {
        if ($this->url && !file_exists($this->url)) {
            $this->stream = null;
        }
        parent::write($record);
    }
}
