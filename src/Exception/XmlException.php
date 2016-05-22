<?php

declare(strict_types = 1);

namespace OctoLab\Common\Exception;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class XmlException extends \InvalidArgumentException
{
    /** @var \LibXMLError[] */
    private $errors;

    /**
     * @param array $errors
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     *
     * @throws \InvalidArgumentException
     *
     * @api
     */
    public function __construct(array $errors, string $message = '', int $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        foreach ($errors as $error) {
            if (!$error instanceof \LibXMLError) {
                throw new \InvalidArgumentException('$errors contains not LibXMLError.', 0, $this);
            }
        }
        $this->errors = $errors;
    }

    /**
     * @return array
     *
     * @api
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
