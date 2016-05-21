<?php

declare(strict_types = 1);

namespace OctoLab\Common\Twig\Extension;

use OctoLab\Common\TestCase;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class AssetExtensionTest extends TestCase
{
    /**
     * @test
     */
    public function getAssetUrl()
    {
        $packages = new Packages();
        $packages->addPackage('test', new Package(new EmptyVersionStrategy()));
        $asset = new AssetExtension($packages);
        self::assertEquals('/path/to/asset.css', $asset->getAssetUrl('@test/path/to/asset.css'));
        self::assertEquals(':pkg/path/to/asset.css', $asset->getAssetUrl('@test:pkg/path/to/asset.css'));
    }
}
