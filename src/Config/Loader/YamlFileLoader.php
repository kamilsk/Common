<?php

namespace OctoLab\Common\Config\Loader;

use OctoLab\Common\Config\Parser\ParserInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\FileLoader;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 *
 * @see \Symfony\Component\DependencyInjection\Loader\YamlFileLoader
 */
class YamlFileLoader extends FileLoader
{
    /** @var array */
    private $content = [];
    /** @var ParserInterface */
    private $parser;

    /**
     * @param FileLocatorInterface $locator
     * @param ParserInterface $parser
     *
     * @api
     */
    public function __construct(FileLocatorInterface $locator, ParserInterface $parser)
    {
        parent::__construct($locator);
        $this->parser = $parser;
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
        return is_string($resource) && !strcasecmp('yml', pathinfo($resource, PATHINFO_EXTENSION));
    }

    /**
     * @param string $file
     *
     * @return mixed
     */
    private function loadFile($file)
    {
        return $this->parser->parse(file_get_contents($file));
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
