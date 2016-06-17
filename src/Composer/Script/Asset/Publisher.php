<?php

declare(strict_types = 1);

namespace OctoLab\Common\Composer\Script\Asset;

use Composer\Package\PackageInterface;
use Composer\Script\Event;
use Symfony\Component\Asset\PackageInterface as AssetPackageInterface;
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
        $assetPackage = $publisher->getAssetPackage($config);
        $composerPackage = $publisher->getComposerPackage($event);
        (new Processor(new Filesystem(), $event->getIO()))->publish(
            $event->getComposer()->getInstallationManager()->getInstallPath($composerPackage),
            $publisher->getPublishingMap($assetPackage, $config),
            $config->isSymlink(),
            $config->isRelative()
        );
    }

    /**
     * @param ConfigInterface $config
     *
     * @return AssetPackageInterface
     *
     * @api
     */
    abstract protected function getAssetPackage(ConfigInterface $config): AssetPackageInterface;

    /**
     * @param Event $event
     *
     * @return PackageInterface
     *
     * @throws \RuntimeException
     *
     * @api
     */
    abstract protected function getComposerPackage(Event $event): PackageInterface;

    /**
     * @param Event $event
     *
     * @return ConfigInterface
     *
     * @throws \InvalidArgumentException
     *
     * @api
     */
    abstract protected function getConfig(Event $event): ConfigInterface;

    /**
     * @param AssetPackageInterface $package
     * @param ConfigInterface $config
     *
     * @return array
     *
     * @api
     */
    abstract protected function getPublishingMap(AssetPackageInterface $package, ConfigInterface $config): array;
}
