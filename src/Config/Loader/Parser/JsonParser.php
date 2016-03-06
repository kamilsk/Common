<?php

namespace OctoLab\Common\Config\Loader\Parser;

use OctoLab\Common\Util\Json;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class JsonParser implements ParserInterface
{
    /** @var Json */
    private $decoder;

    /**
     * @param bool $assoc
     * @param int $options
     * @param int $depth
     *
     * @api
     */
    public function __construct($assoc = true, $options = 0, $depth = 512)
    {
        $this->decoder = new Json($assoc, $options, $depth);
    }

    /**
     * {@inheritdoc}
     */
    public function parse($content)
    {
        return $this->decoder->decode($content);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($extension)
    {
        return 'json' === strtolower($extension);
    }
}
