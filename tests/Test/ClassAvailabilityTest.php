<?php

declare(strict_types = 1);

namespace OctoLab\Common\Test;

use OctoLab\Common\TestCase;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class ClassAvailabilityTest extends TestCase
{
    /**
     * @test
     */
    public function checkAvailability()
    {
        $this->getTest()->checkAvailability();
    }

    /**
     * @test
     */
    public function stopGetFilteredClasses()
    {
        $test = $this->getTest();
        $reflection = (new \ReflectionObject($test))->getMethod('getFilteredClasses');
        $reflection->setAccessible(true);
        /** @var \Generator $generator */
        $generator = $reflection->invoke($test);
        $handled = [];
        foreach ($generator as $class) {
            $handled[] = $class;
            $generator->send(SIGSTOP);
        }
        self::assertCount(1, $handled);
    }

    /**
     * @return ClassAvailability
     */
    protected function getTest(): ClassAvailability
    {
        return new class extends ClassAvailability
        {
            /**
             * {@inheritdoc}
             */
            protected function getClasses(): \Generator
            {
                $classes = [
                    ClassAvailability::class,
                    ClassAvailabilityTest::class,
                ];
                foreach ($classes as $class) {
                    yield $class;
                }
            }

            /**
             * {@inheritdoc}
             */
            protected function isFiltered(string $class): bool
            {
                return false;
            }
        };
    }
}
