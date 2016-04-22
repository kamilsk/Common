<?php

declare(strict_types = 1);

namespace OctoLab\Common\Config\Loader;

use OctoLab\Common\Util\ArrayHelper;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\FileLoader as AbstractFileLoader;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class FileLoader extends AbstractFileLoader
{
    /** @var Parser\ParserInterface */
    private $parser;

    /**
     * @param FileLocatorInterface $locator
     * @param Parser\ParserInterface $parser
     *
     * @api
     */
    public function __construct(FileLocatorInterface $locator, Parser\ParserInterface $parser)
    {
        parent::__construct($locator);
        $this->parser = $parser;
    }

    /**
     * @param string $resource
     * @param null|string $type
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @throws \Symfony\Component\Config\Exception\FileLoaderLoadException
     * @throws \Symfony\Component\Config\Exception\FileLoaderImportCircularReferenceException
     *
     * @api
     */
    public function load($resource, $type = null)
    {
        $path = (string)$this->locator->locate($resource);
        if (!$this->supports($resource)) {
            throw new \InvalidArgumentException(sprintf('File "%s" is not supported.', $resource));
        }
        $fileContent = $this->parser->parse(file_get_contents($path));
        if (!is_array($fileContent)) {
            return [];
        }
        $imports = (array)($fileContent['imports'] ?? []);
        unset($fileContent['imports']);
        $content = $this->loadImports($path, $imports);
        $content[] = $fileContent;
        $content = ArrayHelper::merge(...$content);
        return $content;
    }

    /**
     * @param string $resource
     * @param null|string $type
     *
     * @return bool
     *
     * @api
     */
    public function supports($resource, $type = null): bool
    {
        return $this->parser->supports(pathinfo($resource, PATHINFO_EXTENSION));
    }

    /**
     * @param string $path
     * @param array $imports
     *
     * @return array
     *
     * @throws \Symfony\Component\Config\Exception\FileLoaderImportCircularReferenceException
     * @throws \Symfony\Component\Config\Exception\FileLoaderLoadException
     */
    private function loadImports(string $path, array $imports): array
    {
        $content = [];
        $this->setCurrentDir(dirname($path));
        foreach ($imports as $import) {
            if (is_string($import)) {
                $resource = $import;
                $ignoreErrors = false;
            } else {
                $resource = $import['resource'];
                $ignoreErrors = $import['ignore_errors'] ?? false;
            }
            $content[] = $this->import($resource, null, $ignoreErrors, $path);
        }
        return array_reverse($content);
    }
}
