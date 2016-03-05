<?php

namespace OctoLab\Common\Config\Loader;

use Symfony\Component\Config\FileLocatorInterface;

/**
 * @deprecated moved to {@link FileLoader}
 * @todo will removed since 2.0 version
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class JsonFileLoader extends FileLoader
{
    /** @var int */
    private $depth;
    /** @var int */
    private $options;

    /**
     * @param FileLocatorInterface $locator
     * @param int $depth
     * @param int $options
     *
     * @api
     */
    public function __construct(FileLocatorInterface $locator, $depth = 512, $options = 0)
    {
        trigger_error(sprintf('%s is deprecated.', __CLASS__), E_USER_DEPRECATED);
        parent::__construct($locator);
        $this->depth = $depth;
        $this->options = $options;
        $this->extension = 'json';
    }

    /**
     * @param string $file
     *
     * @return mixed
     */
    protected function loadFile($file)
    {
        return json_decode(file_get_contents($file), true, $this->depth, $this->options);
    }
}
