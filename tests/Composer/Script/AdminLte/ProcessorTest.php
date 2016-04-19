<?php

declare(strict_types = 1);

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
        (new Processor(new Filesystem(), $this->io->reveal()))->publish(
            $targetPath,
            $installPath,
            $map,
            $isSymlink,
            $isRelative
        );
        foreach ($map as $from => $to) {
            self::assertFileExists($installPath . '/' . $from);
            self::assertFileExists($targetPath . '/' . $to);
        }
    }

    /**
     * @test
     */
    public function publishHardCopy()
    {
        $root = rtrim(substr(__DIR__, 0, strrpos(__DIR__, 'tests')), '\\/') . '/tests/app';
        $targetDir = realpath($root . '/web') . '/assets/adminlte';
        $sourceDir = realpath($root . '/vendor/adminlte/dist');

        $filesystem = $this->prophesize(Filesystem::class);
        $filesystem->mkdir($root . '/web/assets', 0777)->willReturn(null);
        $filesystem->remove($targetDir)->willReturn(null);
        $filesystem->symlink($sourceDir, $targetDir)->willThrow(IOException::class);
        $filesystem->mkdir($targetDir, 0777)->willReturn(null);
        $filesystem->mirror($sourceDir, $targetDir)->willReturn(null);

        (new Processor($filesystem->reveal(), $this->io->reveal()))->publish(
            $root . '/web/assets',
            $root . '/vendor/adminlte',
            ['dist' => 'adminlte'],
            true,
            false
        );
        self::assertFileExists($sourceDir);
        self::assertFileExists($targetDir);
    }

    /**
     * @return array[]
     */
    public function configurationProvider()
    {
        $root = rtrim(substr(__DIR__, 0, strrpos(__DIR__, 'tests')), '\\/') . '/tests/app';
        return [
            'default' => [
                $root . '/web/assets',
                $root . '/vendor/adminlte',
                [
                    'dist' => 'adminlte',
                ],
                false,
                false,
            ],
            'with bootstrap' => [
                $root . '/web/assets',
                $root . '/vendor/adminlte',
                [
                    'dist' => 'adminlte',
                    'bootstrap' => 'adminlte-bootstrap',
                ],
                false,
                false,
            ],
            'with plugins' => [
                $root . '/web/assets',
                $root . '/vendor/adminlte',
                [
                    'dist' => 'adminlte',
                    'plugins' => 'adminlte-plugins',
                ],
                false,
                false,
            ],
            'with demo' => [
                $root . '/web/assets',
                $root . '/vendor/adminlte',
                [
                    'dist' => 'adminlte',
                    '' => 'adminlte-demo',
                ],
                false,
                false,
            ],
            'symlink' => [
                $root . '/web/assets',
                $root . '/vendor/adminlte',
                [
                    'dist' => 'adminlte',
                ],
                true,
                false,
            ],
            'relative' => [
                $root . '/web/assets',
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
