<?php

declare(strict_types = 1);

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
     * @param array $placeholders
     *
     * @return FileConfig
     *
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @throws \Symfony\Component\Config\Exception\FileLoaderLoadException
     * @throws \Symfony\Component\Config\Exception\FileLoaderImportCircularReferenceException
     *
     * @api
     */
    public function load(string $resource, array $placeholders = []): FileConfig
    {
        $this->config = $this->fileLoader->load($resource);
        return $this->transform($placeholders);
    }
}
