<?php

declare(strict_types = 1);

namespace OctoLab\Common\Command;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
abstract class Command extends \Symfony\Component\Console\Command\Command
{
    /** @var string */
    private $namespace;

    /**
     * @param null|string $namespace
     *
     * @throws \Symfony\Component\Console\Exception\LogicException
     *
     * @api
     */
    public function __construct(string $namespace = null)
    {
        $this->namespace = $namespace;
        parent::__construct();
    }

    /**
     * Completes the command name with its namespace.
     *
     * @param string $name
     *
     * @return Command
     *
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     *
     * @api
     */
    final public function setName($name): Command
    {
        if (!$this->namespace) {
            parent::setName($name);
            return $this;
        }
        parent::setName(sprintf('%s:%s', $this->namespace, $name));
        return $this;
    }
}
