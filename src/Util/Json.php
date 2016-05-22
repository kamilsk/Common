<?php

declare(strict_types = 1);

namespace OctoLab\Common\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class Json
{
    /** @var bool */
    private $assoc;
    /** @var int */
    private $depth;
    /** @var int */
    private $options;

    /**
     * @param bool $assoc
     * @param int $options
     * @param int $depth
     *
     * @api
     */
    public function __construct(bool $assoc, int $options, int $depth)
    {
        assert('$options >= 0 && $depth >= 0');
        $this->assoc = $assoc;
        $this->options = $options;
        $this->depth = $depth;
    }

    /**
     * @param bool $assoc
     * @param int $options
     * @param int $depth
     *
     * @return Json
     *
     * @api
     */
    public static function new(bool $assoc = false, int $options = 0, int $depth = 512): Json
    {
        return new self($assoc, $options, $depth);
    }

    /**
     * @param string $json
     * @param bool|null $assoc
     * @param int|null $depth
     * @param int|null $options
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     *
     * @api
     */
    public function decode(string $json, bool $assoc = null, int $depth = null, int $options = null)
    {
        list($content, $error) = $this->softDecode($json, $assoc, $depth, $options);
        if ($error !== null) {
            throw $error;
        }
        return $content;
    }

    /**
     * @param mixed $value
     * @param int|null $options
     * @param int|null $depth
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     *
     * @api
     */
    public function encode($value, int $options = null, int $depth = null): string
    {
        list($json, $error) = $this->softEncode($value, $options, $depth);
        if ($error !== null) {
            throw $error;
        }
        return $json;
    }

    /**
     * @param string $json
     * @param bool|null $assoc
     * @param int|null $depth
     * @param int|null $options
     *
     * @return array where first element is a result of json_decode, second - \InvalidArgumentException or null
     *
     * @api
     */
    public function softDecode(string $json, bool $assoc = null, int $depth = null, int $options = null): array
    {
        assert('($depth === null || $depth >= 0) && ($options === null || $options >= 0)');
        $content = json_decode($json, $assoc ?? $this->assoc, $depth ?? $this->depth, $options ?? $this->options);
        return [$content, $this->getError()];
    }

    /**
     * @param mixed $value
     * @param int|null $options
     * @param int|null $depth
     *
     * @return array where first element is a result of json_encode, second - \InvalidArgumentException or null
     *
     * @api
     */
    public function softEncode($value, int $options = null, int $depth = null): array
    {
        assert('($depth === null || $depth >= 0) && ($options === null || $options >= 0)');
        $json = json_encode($value, $options ?? $this->options, $depth ?? $this->depth);
        return [$json, $this->getError()];
    }

    /**
     * @return \InvalidArgumentException|null
     */
    private function getError()
    {
        if (JSON_ERROR_NONE !== json_last_error()) {
            return new \InvalidArgumentException(json_last_error_msg(), json_last_error());
        }
        return null;
    }
}
