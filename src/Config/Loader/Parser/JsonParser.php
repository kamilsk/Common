<?php

declare(strict_types = 1);

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
    public function __construct(bool $assoc = true, int $options = 0, int $depth = 512)
    {
        $this->decoder = Json::new($assoc, $options, $depth);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function parse(string $content)
    {
        return $this->decoder->decode($content);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $extension): bool
    {
        return 'json' === strtolower($extension);
    }
}
