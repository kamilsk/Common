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
     */
    final public function checkAvailability()
    {
        foreach ($this->getFilteredClasses() as $class) {
            self::assertTrue(class_exists($class) || interface_exists($class) || trait_exists($class));
        }
    }

    /**
     * @return \Generator
     */
    abstract protected function getClasses(): \Generator;

    /**
     * @return \Generator
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

    /**
     * @param string $class
     *
     * @return bool
     */
    abstract protected function isFiltered(string $class): bool;
}