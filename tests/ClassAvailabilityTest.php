<?php

namespace Test\OctoLab\Common;

/**
 * phpunit tests/ClassAvailabilityTest.php
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class ClassAvailabilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function classmap()
    {
        foreach ($this->getClasses() as $class) {
            self::assertTrue(class_exists($class) || interface_exists($class) || trait_exists($class));
        }
    }

    /**
     * @return string[]
     */
    private function getClasses()
    {
        $classes = [];
        $excluded = [
            // parent class or interface not found
            '\Zend\EventManager\Filter\FilterIterator' => true,
        ];
        foreach (require dirname(__DIR__) . '/vendor/composer/autoload_classmap.php' as $class => $path) {
            if (empty($excluded['\\' . $class])) {
                $classes[] = $class;
            }
        }
        return $classes;
    }
}
