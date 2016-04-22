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
        $composer = $event->getComposer();
        $config = static::validate($composer->getPackage()->getExtra());
        $package = $composer->getRepositoryManager()->getLocalRepository()
            ->findPackage('almasaeed2010/adminlte', '~2.0');
        if ($package === null) {
            throw new \RuntimeException('The AdminLTE package not found.');
        }
        (new Processor(new Filesystem(), $event->getIO()))->publish(
            $config['target'],
            $composer->getInstallationManager()->getInstallPath($package),
            static::publication($config),
            $config['symlink'],
            $config['relative']
        );
    }

    /**
     * @param array $extra
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    private static function validate(array $extra): array
    {
        if (!isset($extra[self::KEY])) {
            throw new \InvalidArgumentException(
                'The AdminLTE installer needs to be configured through the extra.admin-lte setting.'
            );
        }
        if (!isset($extra[self::KEY]['target'])) {
            throw new \InvalidArgumentException('The extra.admin-lte must contains target path.');
        }
        return $extra[self::KEY] + ['symlink' => false, 'relative' => false];
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private static function publication(array $config): array
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
