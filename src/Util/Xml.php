<?php

declare(strict_types = 1);

namespace OctoLab\Common\Util;

use OctoLab\Common\Exception\XmlException;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class Xml
{
    /** @var string */
    private $className;
    /** @var bool */
    private $isPrefix;
    /** @var string */
    private $namespace;
    /** @var int */
    private $options;

    /**
     * @param string $className
     * @param int $options
     * @param string $ns
     * @param bool $isPrefix
     *
     * @api
     */
    public function __construct(string $className, int $options, string $ns, bool $isPrefix)
    {
        assert('class_exists($className)
            && (ltrim($className, "\\\") === \SimpleXMLElement::class
                || is_subclass_of($className, \SimpleXMLElement::class, true))
            && $options >= 0');
        $this->className = $className;
        $this->options = $options;
        $this->namespace = $ns;
        $this->isPrefix = $isPrefix;
    }

    /**
     * @param string $className
     * @param int $options
     * @param string $ns
     * @param bool $isPrefix
     *
     * @return Xml
     *
     * @api
     */
    public static function new(
        string $className = 'SimpleXMLElement',
        int $options = 0,
        string $ns = '',
        bool $isPrefix = false
    ): Xml {
        return new self($className, $options, $ns, $isPrefix);
    }

    /**
     * @param string $xml
     * @param string|null $className
     * @param int|null $options
     * @param string|null $ns
     * @param bool|null $isPrefix
     *
     * @return \SimpleXMLElement
     *
     * @throws XmlException
     * @throws \InvalidArgumentException
     *
     * @api
     */
    public function parse(
        string $xml,
        string $className = null,
        int $options = null,
        string $ns = null,
        bool $isPrefix = null
    ): \SimpleXMLElement {
        list($content, $error) = $this->softParse($xml, $className, $options, $ns, $isPrefix);
        if ($error !== null) {
            throw $error;
        }
        return $content;
    }

    /**
     * @param string $xml
     * @param string|null $className
     * @param int|null $options
     * @param string|null $ns
     * @param bool|null $isPrefix
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     *
     * @api
     */
    public function softParse(
        string $xml,
        string $className = null,
        int $options = null,
        string $ns = null,
        bool $isPrefix = null
    ): array {
        assert('$options === null || $options >= 0');
        libxml_use_internal_errors(($before = libxml_use_internal_errors()) || true);
        libxml_clear_errors();
        $content = simplexml_load_string(
            $xml,
            $className ?? $this->className,
            $options ?? $this->options,
            $ns ?? $this->namespace,
            $isPrefix ?? $this->isPrefix
        );
        $error = $this->getError($xml);
        libxml_use_internal_errors($before);
        return [$content, $error];
    }

    /**
     * @param string $xml
     *
     * @return XmlException|null
     *
     * @throws \InvalidArgumentException
     */
    private function getError(string $xml)
    {
        if ([] !== ($errors = libxml_get_errors())) {
            libxml_clear_errors();
            return new XmlException($errors, sprintf("Invalid xml string \n\n%s\n", $xml));
        }
        return null;
    }
}
