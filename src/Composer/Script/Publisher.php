<?php

declare(strict_types = 1);

namespace OctoLab\Common\Composer\Script;

use Composer\Package\PackageInterface;
use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
abstract class Publisher
{
    /**
     * @param Event $event
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     *
     * @api
     */
    public static function publish(Event $event)
    {
        $publisher = new static();
        $config = $publisher->getConfig($event);
        $package = $publisher->getPackage($event);
        (new Processor(new Filesystem(), $event->getIO()))->publish(
            $config['target'],
            $event->getComposer()->getInstallationManager()->getInstallPath($package),
            $publisher->getPublishingMap($config),
            $config['symlink'],
            $config['relative']
        );
    }

    /**
     * @param Event $event
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     *
     * @api
     */
    abstract protected function getConfig(Event $event): array;

    /**
     * @param Event $event
     *
     * @return PackageInterface
     *
     * @throws \RuntimeException
     *
     * @api
     */
    abstract protected function getPackage(Event $event): PackageInterface;

    /**
     * @param array $config
     *
     * @return array
     *
     * @api
     */
    abstract protected function getPublishingMap(array $config): array;
}
