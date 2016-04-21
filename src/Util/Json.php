<?php

declare(strict_types = 1);

namespace OctoLab\Common\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class Json
{
    /**
     * @param bool $assoc
     * @param int $options
     * @param int $depth
     *
     * @return Json
     */
    public static function new(bool $assoc = false, int $options = 0, int $depth = 512): Json
    {
        return new self($assoc, $options, $depth);
    }

    /** @var bool */
    private $assoc;
    /** @var int */
    private $options;
    /** @var int */
    private $depth;

    /**
     * @param bool $assoc
     * @param int $options
     * @param int $depth
     *
     * @api
     */
    public function __construct(bool $assoc, int $options, int $depth)
    {
        $this->assoc = $assoc;
        $this->options = $options;
        $this->depth = $depth;
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
        $result = json_decode($json, $assoc ?? $this->assoc, $depth ?? $this->depth, $options ?? $this->options);
        $this->checkError();
        return $result;
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
        $json = json_encode($value, $options ?? $this->options, $depth ?? $this->depth);
        $this->checkError();
        return $json;
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function checkError()
    {
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(json_last_error_msg(), json_last_error());
        }
    }
}
