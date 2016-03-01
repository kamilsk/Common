<?php

namespace OctoLab\Common\Config\Loader;

use OctoLab\Common\Config\Parser\ParserInterface;
use Symfony\Component\Config\FileLocatorInterface;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 *
 * @see \Symfony\Component\DependencyInjection\Loader\YamlFileLoader
 */
class YamlFileLoader extends FileLoader
{
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
        $this->extension = 'yml';
    }

    /**
     * @param string $file
     *
     * @return mixed
     */
    protected function loadFile($file)
    {
        return $this->parser->parse(file_get_contents($file));
    }

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
