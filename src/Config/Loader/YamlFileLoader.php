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
}
