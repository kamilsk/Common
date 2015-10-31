<?php

namespace OctoLab\Common\Monolog\Handler;

use Monolog\Handler\StreamHandler;

/**
 * Restores stream file descriptor after deleting it when logger is still running.
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class BulletproofStreamHandler extends StreamHandler
{
    /**
     * {@inheritdoc}
     *
     * @api
     */
    protected function write(array $record)
    {
        if ($this->url && !file_exists($this->url)) {
            $this->stream = fopen($this->url, 'a');
            if ($this->filePermission !== null) {
                chmod($this->url, $this->filePermission);
            }
        }
        parent::write($record);
    }
}
