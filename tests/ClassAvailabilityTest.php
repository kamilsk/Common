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
        'Composer\\Semver\\Constraint\\AbstractConstraint' => true,
        'Composer\\Util\\SpdxLicense' => true,
        // no dependencies
        'Symfony\\Bridge\\Twig\\DataCollector\\TwigDataCollector' => true,
        'Symfony\\Bridge\\Twig\\TwigEngine' => true,
        'Zend\\EventManager\\Filter\\FilterIterator' => true,
    ];
    const GROUP_EXCLUDED = [
        // deprecated
        'Composer\\Package\\LinkConstraint' => true,
        // no dependencies
        'Symfony\\Bridge\\Twig\\Form' => true,
        'Symfony\\Bridge\\Twig\\Translation' => true,
        'Symfony\\Component\\Console\\Event' => true,
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
        foreach (self::GROUP_EXCLUDED as $group => $isOn) {
            if ($isOn && strpos($class, $group) === 0) {
                return true;
            }
        }
        return array_key_exists($class, self::EXCLUDED) && self::EXCLUDED[$class];
    }
}
