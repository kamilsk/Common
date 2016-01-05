<?php

namespace OctoLab\Common\Config\Loader;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\FileLoader;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class JsonFileLoader extends FileLoader
{
    /** @var array */
    private $content = [];
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
        parent::__construct($locator);
        $this->depth = $depth;
        $this->options = $options;
    }

    /**
     * @return array
     *
     * @api
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $resource
     * @param string $type
     *
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Config\Exception\FileLoaderLoadException
     *
     * @api
     */
    public function load($resource, $type = null)
    {
        $path = (string) $this->locator->locate($resource);
        $content = $this->loadFile($path);
        if (null === $content) {
            return;
        }
        $this->content[] = $content;
        $this->parseImports($content, $path);
    }

    /**
     * @param string $resource
     * @param string $type
     *
     * @return bool
     *
     * @api
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && !strcasecmp('json', pathinfo($resource, PATHINFO_EXTENSION));
    }

    /**
     * @param string $file
     *
     * @return mixed
     */
    private function loadFile($file)
    {
        return json_decode(file_get_contents($file), true, $this->depth, $this->options);
    }

    /**
     * @param array $content
     * @param string $sourceResource
     *
     * @throws \Symfony\Component\Config\Exception\FileLoaderLoadException
     */
    private function parseImports($content, $sourceResource)
    {
        if (!isset($content['imports'])) {
            return;
        }
        $this->setCurrentDir(dirname($sourceResource));
        foreach ($content['imports'] as $import) {
            if (isset($import['resource'])) {
                $ignoreErrors = isset($import['ignore_errors']) ? (bool) $import['ignore_errors'] : false;
                $this->import($import['resource'], null, $ignoreErrors, $sourceResource);
            }
        }
    }
}
