<?php

declare(strict_types = 1);

namespace OctoLab\Common\Composer\Script\AdminLte;

use Composer\Script\Event;
use OctoLab\Common\Asset\AdminLtePackage;
use OctoLab\Common\Composer\Script\ConfigInterface;
use Symfony\Component\Asset\PackageInterface as AssetPackageInterface;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class Publisher extends \OctoLab\Common\Composer\Script\Publisher
{
    /**
     * {@inheritdoc}
     */
    protected function getAssetPackage(ConfigInterface $config): AssetPackageInterface
    {
        return new AdminLtePackage($config->getTargetPath(), new EmptyVersionStrategy());
    }

    /**
     * {@inheritdoc}
     */
    protected function getComposerPackage(Event $event): \Composer\Package\PackageInterface
    {
        $package = $event->getComposer()->getRepositoryManager()->getLocalRepository()
            ->findPackage('almasaeed2010/adminlte', '~2.0');
        if ($package === null) {
            throw new \RuntimeException('The AdminLTE package not found.');
        }
        return $package;
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfig(Event $event): ConfigInterface
    {
        $extra = $event->getComposer()->getPackage()->getExtra();
        if (!isset($extra['admin-lte'])) {
            throw new \InvalidArgumentException(
                'The AdminLTE installer needs to be configured through the extra.admin-lte setting.'
            );
        }
        return new Config($extra['admin-lte']);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    protected function getPublishingMap(AssetPackageInterface $package, ConfigInterface $config): array
    {
        /**
         * internals!
         * @var Config $config
         * @var AdminLtePackage $package
         */
        $forPublishing = $package->getDistMap();
        if ($config->isBootstrapEnabled()) {
            $forPublishing += $package->getBootstrapMap();
        }
        if ($config->isDemoEnabled()) {
            $forPublishing += $package->getDemoMap();
        }
        if ($config->isPluginsEnabled()) {
            $forPublishing += $package->getPluginsMap();
        }
        return $forPublishing;
    }
}
