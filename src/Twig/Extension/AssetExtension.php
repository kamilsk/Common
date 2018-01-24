<?php

declare(strict_types = 1);

namespace OctoLab\Common\Twig\Extension;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class AssetExtension extends \Symfony\Bridge\Twig\Extension\AssetExtension
{
    /**
     * {@inheritdoc}
     */
    public function getAssetUrl($path, $packageName = null): string
    {
        \assert('is_string($path) && ($packageName === null || is_string($packageName))');
        if ($packageName === null && preg_match('/^@([^:\/]*)(.*)$/', $path, $matches) === 1) {
            list(, $packageName, $path) = $matches;
        }
        return parent::getAssetUrl($path, $packageName);
    }
}
