<?php

declare(strict_types = 1);

namespace OctoLab\Common\Composer\Script;

use Composer\IO\IOInterface;
use OctoLab\Common\TestCase;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class ProcessorTest extends TestCase
{
    /** @var IOInterface */
    private $io;

    /**
     * @return array
     */
    public function configurationProvider(): array
    {
        $appDir = $this->getAppDir();
        return [
            'default' => [
                'packagePath' => $appDir . '/vendor/adminlte',
                'map' => [
                    '/dist' => $appDir . '/web/assets/dist',
                ],
                'isSymlink' => false,
                'isRelative' => false,
            ],
            'with bootstrap' => [
                'packagePath' => $appDir . '/vendor/adminlte',
                'map' => [
                    '/dist' => $appDir . '/web/assets/dist',
                    '/bootstrap' => $appDir . '/web/assets/bootstrap',
                ],
                'isSymlink' => false,
                'isRelative' => false,
            ],
            'with plugins' => [
                'packagePath' => $appDir . '/vendor/adminlte',
                'map' => [
                    '/dist' => $appDir . '/web/assets/dist',
                    '/plugins' => $appDir . '/web/assets/plugins',
                ],
                'isSymlink' => false,
                'isRelative' => false,
            ],
            'with demo' => [
                'packagePath' => $appDir . '/vendor/adminlte',
                'map' => [
                    '/dist' => $appDir . '/web/assets/dist',
                    '/' => $appDir . '/web/assets/demo',
                ],
                'isSymlink' => false,
                'isRelative' => false,
            ],
            'is symlink' => [
                'packagePath' => $appDir . '/vendor/adminlte',
                'map' => [
                    '/dist' => $appDir . '/web/assets/dist',
                ],
                'isSymlink' => true,
                'isRelative' => false,
            ],
            'is relative' => [
                'packagePath' => $appDir . '/vendor/adminlte',
                'map' => [
                    '/dist' => $appDir . '/web/assets/dist',
                ],
                'isSymlink' => true,
                'isRelative' => true,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider configurationProvider
     *
     * @param string $packagePath
     * @param array $map
     * @param bool $isSymlink
     * @param bool $isRelative
     */
    public function publish(string $packagePath, array $map, bool $isSymlink, bool $isRelative)
    {
        (new Processor(new Filesystem(), $this->io->reveal()))->publish(
            $packagePath,
            $map,
            $isSymlink,
            $isRelative
        );
        foreach ($map as $from => $to) {
            self::assertFileExists($packagePath . '/' . $from);
            self::assertFileExists($to);
        }
    }

    /**
     * @test
     */
    public function symlinkFails()
    {
        $appDir = $this->getAppDir();
        $packagePath = realpath($appDir . '/vendor/adminlte');
        $from = '/dist';
        $to = $appDir . '/web/assets/dist';
        $origin = realpath(rtrim($packagePath, '/') . '/' . ltrim($from, '/'));
        $filesystem = $this->prophesize(Filesystem::class);
        $filesystem->remove($to)->willReturn(null)->shouldBeCalledTimes(1);
        $filesystem->mkdir(dirname($to))->willReturn(null)->shouldBeCalledTimes(1);
        $filesystem->symlink($origin, $to)->willThrow(IOException::class)->shouldBeCalledTimes(1);
        $filesystem->mirror($origin, $to)->willReturn(null)->shouldBeCalledTimes(1);
        (new Processor($filesystem->reveal(), $this->io->reveal()))->publish($packagePath, [$from => $to], true, false);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->io = $this->prophesize(IOInterface::class);
    }
}
