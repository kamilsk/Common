<?php

namespace OctoLab\Common\Monolog\Handler;

use Monolog\Handler\StreamHandler;

/**
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
            // customErrorHandler is private, it is really required here?
            $this->stream = fopen($this->url, 'a');
            if ($this->filePermission !== null) {
                @chmod($this->url, $this->filePermission);
            }
        }
        parent::write($record);
    }
}
