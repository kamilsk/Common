<?php

namespace OctoLab\Common\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class Json
{
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
    public function __construct($assoc = false, $options = 0, $depth = 512)
    {
        $this->assoc = $assoc;
        $this->options = $options;
        $this->depth = $depth;
    }

    /**
     * @todo up code quality [B]
     *
     * @param string $json
     * @param bool|null $assoc
     * @param int|null $depth
     * @param int|null $options
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException when
     *  JSON_ERROR_STATE_MISMATCH
     *  JSON_ERROR_CTRL_CHAR
     *  JSON_ERROR_SYNTAX
     *  JSON_ERROR_UTF8
     *  JSON_ERROR_INF_OR_NAN
     *  JSON_ERROR_UNSUPPORTED_TYPE
     * @throws \OverflowException when
     *  JSON_ERROR_DEPTH
     *  JSON_ERROR_RECURSION
     * @throws \UnexpectedValueException otherwise
     *
     * @api
     */
    public function decode($json, $assoc = null, $depth = null, $options = null)
    {
        $assoc = $assoc === null ? $this->assoc : $assoc;
        $options = $options === null ? $this->options : $options;
        $depth = $depth === null ? $this->depth : $depth;
        $result = json_decode($json, $assoc, $depth, $options);
        if (json_last_error()) {
            throw $this->getException();
        }
        return $result;
    }

    /**
     * @param mixed $value
     * @param int|null $options
     * @param int|null $depth
     *
     * @return string
     *
     * @throws \InvalidArgumentException when
     *  JSON_ERROR_STATE_MISMATCH
     *  JSON_ERROR_CTRL_CHAR
     *  JSON_ERROR_SYNTAX
     *  JSON_ERROR_UTF8
     *  JSON_ERROR_INF_OR_NAN
     *  JSON_ERROR_UNSUPPORTED_TYPE
     * @throws \OverflowException when
     *  JSON_ERROR_DEPTH
     *  JSON_ERROR_RECURSION
     * @throws \UnexpectedValueException otherwise
     *
     * @api
     */
    public function encode($value, $options = null, $depth = null)
    {
        $options = $options === null ? $this->options : $options;
        $depth = $depth === null ? $this->depth : $depth;
        $json = json_encode($value, $options, $depth);
        $error = json_last_error();
        if ($error) {
            throw $this->getException();
        }
        return $json;
    }

    /**
     * @todo up code quality [B]
     *
     * @return \InvalidArgumentException|\OverflowException|\UnexpectedValueException
     */
    private function getException()
    {
        $message = json_last_error_msg();
        switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
            case JSON_ERROR_RECURSION:
                return new \OverflowException($message);
            case JSON_ERROR_STATE_MISMATCH:
            case JSON_ERROR_CTRL_CHAR:
            case JSON_ERROR_SYNTAX:
            case JSON_ERROR_UTF8:
            case JSON_ERROR_INF_OR_NAN:
            case JSON_ERROR_UNSUPPORTED_TYPE:
                return new \InvalidArgumentException($message);
            default:
                return new \UnexpectedValueException($message);
        }
    }
}
