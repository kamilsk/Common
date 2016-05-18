<?php

declare(strict_types = 1);

namespace OctoLab\Common\Test;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
abstract class ClassAvailability extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     *
     * @api
     */
    final public function checkAvailability()
    {
        foreach ($this->getFilteredClasses() as $class) {
            self::assertTrue(class_exists($class) || interface_exists($class) || trait_exists($class));
        }
    }

    /**
     * @return \Generator
     *
     * @api
     */
    abstract protected function getClasses(): \Generator;

    /**
     * @param string $class
     *
     * @return bool
     *
     * @api
     */
    abstract protected function isFiltered(string $class): bool;

    /**
     * @return \Generator
     *
     * @api
     */
    final protected function getFilteredClasses(): \Generator
    {
        foreach ($this->getClasses() as $class) {
            if (!$this->isFiltered($class)) {
                $signal = yield $class;
                if (SIGSTOP === $signal) {
                    return;
                }
            }
        }
    }
}
