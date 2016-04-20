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
        return new static($assoc, $options, $depth);
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
    public function __construct(bool $assoc = false, int $options = 0, int $depth = 512)
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
     * @throws \OverflowException when
     *  JSON_ERROR_DEPTH
     *  JSON_ERROR_RECURSION
     * @throws \InvalidArgumentException when
     *  JSON_ERROR_STATE_MISMATCH
     *  JSON_ERROR_CTRL_CHAR
     *  JSON_ERROR_SYNTAX
     *  JSON_ERROR_UTF8
     *  JSON_ERROR_INF_OR_NAN
     *  JSON_ERROR_UNSUPPORTED_TYPE
     *
     * @api
     *
     * @quality:method [B]
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
     * @throws \OverflowException when
     *  JSON_ERROR_DEPTH
     *  JSON_ERROR_RECURSION
     * @throws \InvalidArgumentException when
     *  JSON_ERROR_STATE_MISMATCH
     *  JSON_ERROR_CTRL_CHAR
     *  JSON_ERROR_SYNTAX
     *  JSON_ERROR_UTF8
     *  JSON_ERROR_INF_OR_NAN
     *  JSON_ERROR_UNSUPPORTED_TYPE
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
     * @throws \OverflowException
     * @throws \InvalidArgumentException
     *
     * @quality:method [B]
     */
    private function checkError()
    {
        if (JSON_ERROR_NONE !== json_last_error()) {
            $message = json_last_error_msg();
            switch (json_last_error()) {
                case JSON_ERROR_DEPTH:
                case JSON_ERROR_RECURSION:
                    throw new \OverflowException($message);
                case JSON_ERROR_STATE_MISMATCH:
                case JSON_ERROR_CTRL_CHAR:
                case JSON_ERROR_SYNTAX:
                case JSON_ERROR_UTF8:
                case JSON_ERROR_INF_OR_NAN:
                case JSON_ERROR_UNSUPPORTED_TYPE:
                    throw new \InvalidArgumentException($message);
            }
        }
    }
}
