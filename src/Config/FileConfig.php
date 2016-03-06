<?php

namespace OctoLab\Common\Config;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class FileConfig extends SimpleConfig
{
    /** @var Loader\FileLoader */
    private $fileLoader;

    /**
     * @param Loader\FileLoader $fileLoader
     *
     * @api
     */
    public function __construct(Loader\FileLoader $fileLoader)
    {
        parent::__construct();
        $this->fileLoader = $fileLoader;
    }

    /**
     * @param string $resource
     * @param bool $check
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Config\Exception\FileLoaderLoadException
     * @throws \DomainException
     *
     * @api
     */
    public function load($resource, $check = false)
    {
        if ($check && !$this->fileLoader->supports($resource)) {
            throw new \DomainException(sprintf('File "%s" is not supported.', $resource));
        }
        $this->config = $this->fileLoader->load($resource);
        return $this;
    }
}
