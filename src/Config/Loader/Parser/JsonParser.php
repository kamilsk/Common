<?php

declare(strict_types = 1);

namespace OctoLab\Common\Config\Loader\Parser;

use OctoLab\Common\Util\Json;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class JsonParser implements ParserInterface
{
    /** @var Json */
    private $serializer;

    /**
     * @param bool $assoc
     * @param int $options
     * @param int $depth
     *
     * @api
     */
    public function __construct(bool $assoc = true, int $options = 0, int $depth = 8)
    {
        $this->serializer = new Json($assoc, $options, $depth);
    }

    /**
     * {@inheritdoc}
     */
    public function parse(string $content)
    {
        return $this->serializer->decode($content);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $extension): bool
    {
        return 'json' === strtolower($extension);
    }
}
