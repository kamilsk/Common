<?php

declare(strict_types = 1);

namespace OctoLab\Common\Asset;

use OctoLab\Common\TestCase;
use Symfony\Component\Asset\Context\ContextInterface;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class AdminLtePackageTest extends TestCase
{
    /**
     * @test
     * @dataProvider urlProvider
     *
     * @param string $path
     * @param string $url
     */
    public function getUrl(string $path, string $url)
    {
        $packageWithAssetContext = new AdminLtePackage('/adminlte', new EmptyVersionStrategy(), $this->getContext());
        $packageWithoutAssetContext = new AdminLtePackage('/assets/adminlte', new EmptyVersionStrategy());
        self::assertEquals($url, $packageWithAssetContext->getUrl($path));
        self::assertEquals($url, $packageWithoutAssetContext->getUrl($path));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage No path "/some.css" for subpackage ":fail".
     */
    public function getUrlFailure()
    {
        self::assertEquals(
            '/assets/adminlte/fail/some.css',
            (new AdminLtePackage('/assets/adminlte', new EmptyVersionStrategy()))->getUrl(':fail/some.css')
        );
    }

    /**
     * @test
     */
    public function getBootstrapMap()
    {
        self::assertEquals(['/bootstrap' => '/assets/bootstrap'], $this->getPackage()->getBootstrapMap());
    }

    /**
     * @test
     */
    public function getDemoMap()
    {
        self::assertEquals(['/' => '/assets/demo'], $this->getPackage()->getDemoMap());
    }

    /**
     * @test
     */
    public function getDistMap()
    {
        self::assertEquals(['/dist' => '/assets/dist'], $this->getPackage()->getDistMap());
    }

    /**
     * @test
     */
    public function getPluginsMap()
    {
        self::assertEquals(['/plugins' => '/assets/plugins'], $this->getPackage()->getPluginsMap());
    }

    /**
     * @return array
     */
    public function urlProvider(): array
    {
        return [
            'resolve bootstrap' => [':bootstrap/css/bootstrap.css', '/assets/adminlte/bootstrap/css/bootstrap.css'],
            'resolve demo' => [':demo/index.html', '/assets/adminlte/demo/index.html'],
            'resolve dist' => [':dist/css/AdminLTE.css', '/assets/adminlte/dist/css/AdminLTE.css'],
            'default resolving' => ['/css/AdminLTE.css', '/assets/adminlte/dist/css/AdminLTE.css'],
            'resolve plugins' =>
                [':plugins/bootstrap-slider/slider.css', '/assets/adminlte/plugins/bootstrap-slider/slider.css'],
            'resolve empty path' => [':dist', '/assets/adminlte/dist'],
        ];
    }

    /**
     * @return ContextInterface
     */
    private function getContext(): ContextInterface
    {
        return new class implements ContextInterface
        {
            /**
             * {@inheritdoc}
             */
            public function getBasePath()
            {
                return '/assets';
            }

            /**
             * {@inheritdoc}
             */
            public function isSecure(): bool
            {
                return false;
            }
        };
    }

    /**
     * @return AdminLtePackage
     */
    private function getPackage(): AdminLtePackage
    {
        return new AdminLtePackage('/', new EmptyVersionStrategy(), $this->getContext());
    }
}
