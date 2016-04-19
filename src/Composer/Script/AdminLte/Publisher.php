<?php

declare(strict_types = 1);

namespace OctoLab\Common\Composer\Script\AdminLte;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class Publisher
{
    /**
     * @param Event $event
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     *
     * @api
     *
     * @quality:method [C]
     */
    public static function publish(Event $event)
    {
        $composer = $event->getComposer();
        $extras = $composer->getPackage()->getExtra();
        if (!isset($extras['admin-lte'])) {
            throw new \InvalidArgumentException(
                'The AdminLTE installer needs to be configured through the extra.admin-lte setting.'
            );
        }
        $config = $extras['admin-lte'];
        if (!isset($config['target'])) {
            throw new \InvalidArgumentException('The extra.admin-lte must contains target path.');
        }
        $package = $composer->getRepositoryManager()->getLocalRepository()
            ->findPackage('almasaeed2010/adminlte', '~2.0');
        if ($package === null) {
            throw new \RuntimeException('The AdminLTE package not found.');
        }

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

        (new Processor(new Filesystem(), $event->getIO()))->publish(
            $config['target'],
            $composer->getInstallationManager()->getInstallPath($package),
            $forPublishing,
            !empty($config['symlink']),
            !empty($config['relative'])
        );
    }
}
