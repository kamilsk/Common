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
        'Symfony\\Bridge\\Twig\\DataCollector\\TwigDataCollector' => ['symfony/http-foundation', 'symfony/http-kernel'],
        'Symfony\\Bridge\\Twig\\TwigEngine' => ['symfony/templating'],
        'Zend\\EventManager\\Filter\\FilterIterator' => ['zendframework/zend-stdlib'],
    ];
    const GROUP_EXCLUDED = [
        // deprecated
        'Composer\\Package\\LinkConstraint' => true,
        // no dependencies
        'Symfony\\Bridge\\Twig\\Form' => [
            'symfony/expression-language',
            'symfony/form',
            'symfony/http-foundation',
            'symfony/http-kernel',
            'symfony/routing',
            'symfony/security',
            'symfony/stopwatch',
            'symfony/translation',
            'symfony/var-dumper',
        ],
        'Symfony\\Bridge\\Twig\\Translation' => ['symfony/translation'],
        'Symfony\\Component\\Console\\Event' => ['symfony/event-dispatcher'],
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
