<?php

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
     * @quality [C]
     *
     * @param string $resource
     * @param string|null $type
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
        $content = [];
        $path = (string)$this->locator->locate($resource);
        if (!$this->supports($resource)) {
            throw new \InvalidArgumentException(sprintf('File "%s" is not supported.', $resource));
        }
        $fileContent = $this->loadFile($path);
        if ($fileContent === null) {
            return $content;
        }
        $content[] = $fileContent;
        if (isset($fileContent['imports'])) {
            $this->setCurrentDir(dirname($path));
            foreach ($fileContent['imports'] as $import) {
                if (is_string($import)) {
                    $resource = $import;
                    $ignoreErrors = false;
                } else {
                    $resource = $import['resource'];
                    $ignoreErrors = isset($import['ignore_errors']) ? (bool)$import['ignore_errors'] : false;
                }
                $content[] = $this->import($resource, null, $ignoreErrors, $path);
            }
        }
        $content = call_user_func_array([ArrayHelper::class, 'merge'], array_reverse($content));
        unset($content['imports']);
        return $content;
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
        return $this->parser->supports(pathinfo($resource, PATHINFO_EXTENSION));
    }

    /**
     * @param string $file
     *
     * @return mixed
     *
     * @throws \Exception
     */
    private function loadFile($file)
    {
        return $this->parser->parse(file_get_contents($file));
    }
}
