<?php

declare(strict_types = 1);

namespace OctoLab\Common;

use OctoLab\Common\Test\ClassAvailability;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class ClassAvailabilityTest extends ClassAvailability
{
    const EXCLUDED = [
        // deprecated
        'Composer\\Package\\LinkConstraint\\EmptyConstraint' => true,
        'Composer\\Package\\LinkConstraint\\LinkConstraintInterface' => true,
        'Composer\\Package\\LinkConstraint\\MultiConstraint' => true,
        'Composer\\Package\\LinkConstraint\\SpecificConstraint' => true,
        'Composer\\Package\\LinkConstraint\\VersionConstraint' => true,
        'Composer\\Semver\\Constraint\\AbstractConstraint' => true,
        'Composer\\Util\\SpdxLicense' => true,
        // no dependencies
        'Symfony\\Component\\Console\\Event\\ConsoleCommandEvent' => true,
        'Symfony\\Component\\Console\\Event\\ConsoleEvent' => true,
        'Symfony\\Component\\Console\\Event\\ConsoleExceptionEvent' => true,
        'Symfony\\Component\\Console\\Event\\ConsoleTerminateEvent' => true,
        'Zend\\EventManager\\Filter\\FilterIterator' => true,
    ];

    /**
     * {@inheritdoc}
     */
    protected function getClasses(): \Generator
    {
        foreach (require dirname(__DIR__) . '/vendor/composer/autoload_classmap.php' as $class => $path) {
            $signal = yield $class;
            if (SIGSTOP === $signal) {
                return;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function isFiltered(string $class): bool
    {
        return array_key_exists($class, self::EXCLUDED);
    }
}
