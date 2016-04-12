<?php

namespace OctoLab\Common\Composer\Script\AdminLte;

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

    protected function setUp()
    {
        parent::setUp();
        $this->io = $this->prophesize(IOInterface::class);
    }

    /**
     * @test
     * @dataProvider configurationProvider
     *
     * @param string $targetPath
     * @param string $installPath
     * @param array $map
     * @param bool $isSymlink
     * @param bool $isRelative
     */
    public function publish($targetPath, $installPath, array $map, $isSymlink, $isRelative)
    {
        self::assertTrue((new Processor(new Filesystem(), $this->io->reveal()))->publish(
            $targetPath,
            $installPath,
            $map,
            $isSymlink,
            $isRelative
        ));
    }

    /**
     * @test
     */
    public function publishHardCopy()
    {
        $root = rtrim(substr(__DIR__, 0, strrpos(__DIR__, 'tests')), '\\/') . '/tests/app';
        $targetDir = realpath($root . '/web') . '/adminlte';
        $sourceDir = realpath($root . '/vendor/adminlte') . '/dist';

        $filesystem = $this->prophesize(Filesystem::class);
        $filesystem->mkdir($root . '/web', 0777)->willReturn(null);
        $filesystem->remove($targetDir)->willReturn(null);
        $filesystem->symlink($sourceDir, $targetDir)->willThrow(IOException::class);
        $filesystem->mkdir($targetDir, 0777)->willReturn(null);
        $filesystem->mirror($sourceDir, $targetDir)->willReturn(null);

        self::assertTrue((new Processor($filesystem->reveal(), $this->io->reveal()))->publish(
            $root . '/web',
            $root . '/vendor/adminlte',
            ['dist' => 'adminlte'],
            true,
            false
        ));
    }

    /**
     * @return array[]
     */
    public function configurationProvider()
    {
        $root = rtrim(substr(__DIR__, 0, strrpos(__DIR__, 'tests')), '\\/') . '/tests/app';
        return [
            'default' => [
                $root . '/web',
                $root . '/vendor/adminlte',
                [
                    'dist' => 'adminlte',
                ],
                false,
                false,
            ],
            'with bootstrap' => [
                $root . '/web',
                $root . '/vendor/adminlte',
                [
                    'dist' => 'adminlte',
                    'bootstrap' => 'adminlte-bootstrap',
                ],
                false,
                false,
            ],
            'with plugins' => [
                $root . '/web',
                $root . '/vendor/adminlte',
                [
                    'dist' => 'adminlte',
                    'bootstrap' => 'adminlte-bootstrap',
                    'plugins' => 'adminlte-plugins',
                ],
                false,
                false,
            ],
            'symlink' => [
                $root . '/web',
                $root . '/vendor/adminlte',
                [
                    'dist' => 'adminlte',
                ],
                true,
                false,
            ],
            'relative' => [
                $root . '/web',
                $root . '/vendor/adminlte',
                [
                    'dist' => 'adminlte',
                ],
                true,
                true,
            ],
        ];
    }
}
