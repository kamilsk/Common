<?php

namespace OctoLab\Common\Config\Loader;

use OctoLab\Common\Config\Parser\ParserInterface;
use Symfony\Component\Config\FileLocatorInterface;

/**
 * @deprecated moved to {@link FileLoader}
 * @todo will removed since 2.0 version
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
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
        trigger_error(sprintf('%s is deprecated.', __CLASS__), E_USER_DEPRECATED);
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
}
