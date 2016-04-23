<?php

declare(strict_types = 1);

namespace OctoLab\Common;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class ClassAvailabilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    final public function classmap()
    {
        foreach ($this->getFilteredClasses() as $class) {
            self::assertTrue(class_exists($class) || interface_exists($class) || trait_exists($class));
        }
    }

    /**
     * @return \Generator
     */
    protected function getFilteredClasses(): \Generator
    {
        $excluded = [
            // deprecated
            '\Composer\Package\LinkConstraint\EmptyConstraint' => true,
            '\Composer\Package\LinkConstraint\LinkConstraintInterface' => true,
            '\Composer\Package\LinkConstraint\MultiConstraint' => true,
            '\Composer\Package\LinkConstraint\SpecificConstraint' => true,
            '\Composer\Package\LinkConstraint\VersionConstraint' => true,
            '\Composer\Semver\Constraint\AbstractConstraint' => true,
            '\Composer\Util\SpdxLicense' => true,
            // parent class or interface not found
            '\Zend\EventManager\Filter\FilterIterator' => true,
        ];
        foreach ($this->getClasses() as list($path, $class)) {
            if (empty($excluded[$class]) && empty($excluded['\\' . $class])) {
                $signal = yield $class;
                if (SIGSTOP === $signal) {
                    return;
                }
            }
        }
    }

    /**
     * @return \Generator
     */
    final protected function getClasses(): \Generator
    {
        foreach (require dirname(__DIR__) . '/vendor/composer/autoload_classmap.php' as $class => $path) {
            yield [$path, $class];
        }
    }
}
