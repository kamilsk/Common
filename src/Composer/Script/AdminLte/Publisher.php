<?php

declare(strict_types = 1);

namespace OctoLab\Common\Composer\Script\AdminLte;

use Composer\Package\PackageInterface;
use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class Publisher
{
    const KEY = 'admin-lte';

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
        $config = static::getConfig($event);
        $package = static::getPackage($event);
        (new Processor(new Filesystem(), $event->getIO()))->publish(
            $config['target'],
            $event->getComposer()->getInstallationManager()->getInstallPath($package),
            static::getPublishingMap($config),
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
     */
    protected static function getConfig(Event $event): array
    {
        $extra = $event->getComposer()->getPackage()->getExtra();
        if (!isset($extra[static::KEY])) {
            throw new \InvalidArgumentException(
                'The AdminLTE installer needs to be configured through the extra.admin-lte setting.'
            );
        }
        if (!isset($extra[static::KEY]['target'])) {
            throw new \InvalidArgumentException('The extra.admin-lte must contains target path.');
        }
        return $extra[static::KEY] + ['symlink' => false, 'relative' => false];
    }

    /**
     * @param Event $event
     *
     * @return PackageInterface
     *
     * @throws \RuntimeException
     */
    protected static function getPackage(Event $event): PackageInterface
    {
        $package = $event->getComposer()->getRepositoryManager()->getLocalRepository()
            ->findPackage('almasaeed2010/adminlte', '~2.0');
        if ($package === null) {
            throw new \RuntimeException('The AdminLTE package not found.');
        }
        return $package;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    protected static function getPublishingMap(array $config): array
    {
        $forPublishing = ['dist' => 'adminlte'];
        if (!empty($config['bootstrap'])) {
            $forPublishing['bootstrap'] = 'adminlte-bootstrap';
        }
        if (!empty($config['plugins'])) {
            $forPublishing['plugins'] = 'adminlte-plugins';
        }
        if (!empty($config['demo'])) {
            $forPublishing[''] = 'adminlte-demo';
        }
        return $forPublishing;
    }
}
