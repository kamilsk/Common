<?php

declare(strict_types = 1);

namespace OctoLab\Common\Composer\Script\AdminLte;

use Composer\Package\PackageInterface;
use Composer\Script\Event;
use OctoLab\Common\Composer\Script\PublisherConfig;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class Publisher extends \OctoLab\Common\Composer\Script\Publisher
{
    /**
     * {@inheritdoc}
     */
    protected function getConfig(Event $event): PublisherConfig
    {
        $extra = $event->getComposer()->getPackage()->getExtra();
        if (!isset($extra['admin-lte'])) {
            throw new \InvalidArgumentException(
                'The AdminLTE installer needs to be configured through the extra.admin-lte setting.'
            );
        }
        return new PublisherConfig('admin-lte', $extra['admin-lte']);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackage(Event $event): PackageInterface
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
    protected function getPublishingMap(PublisherConfig $config): array
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
