<?php

namespace OctoLab\Common\Config;

use OctoLab\Common\Config\Util\ArrayHelper;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class FileConfig extends SimpleConfig
{
    /** @var Loader\JsonFileLoader */
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
        $this->fileLoader->load($resource);
        foreach (array_reverse($this->fileLoader->getContent()) as $data) {
            $this->config = ArrayHelper::merge($this->config, $data);
        }
        return $this;
    }
}
