<?php

namespace OctoLab\Common\Config;

/**
 * @deprecated moved to {@link FileConfig}
 * @todo will removed since 2.0 version
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class JsonConfig extends FileConfig
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Loader\FileLoader $fileLoader)
    {
        trigger_error(sprintf('%s is deprecated.', __CLASS__), E_USER_DEPRECATED);
        parent::__construct($fileLoader);
    }
}
