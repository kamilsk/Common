<?php

namespace OctoLab\Common\Config\Loader;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\FileLoader as AbstractFileLoader;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
abstract class FileLoader extends AbstractFileLoader
{
    /** @var array */
    protected $content;
    /** @var string */
    protected $extension;

    /**
     * @param FileLocatorInterface $locator
     *
     * @api
     */
    public function __construct(FileLocatorInterface $locator)
    {
        parent::__construct($locator);
        $this->content = [];
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
     * @param string|null $type
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
     * @param string|null $type
     *
     * @return bool
     *
     * @api
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && !strcasecmp($this->extension, pathinfo($resource, PATHINFO_EXTENSION));
    }

    /**
     * @param string $file
     *
     * @return mixed
     */
    abstract protected function loadFile($file);

    /**
     * @param array $content
     * @param string $sourceResource
     *
     * @throws \Symfony\Component\Config\Exception\FileLoaderLoadException
     */
    protected function parseImports($content, $sourceResource)
    {
        if (!isset($content['imports'])) {
            return;
        }
        $this->setCurrentDir(dirname($sourceResource));
        foreach ($content['imports'] as $import) {
            $isString = is_string($import);
            $hasResource = !$isString && isset($import['resource']);
            if ($isString || $hasResource) {
                if ($isString) {
                    $resource = $import;
                    $ignoreErrors = false;
                } else {
                    $resource = $import['resource'];
                    $ignoreErrors = isset($import['ignore_errors']) ? (bool) $import['ignore_errors'] : false;
                }
                $this->import($resource, null, $ignoreErrors, $sourceResource);
            }
        }
    }
}
