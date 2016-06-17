<?php

declare(strict_types = 1);

namespace OctoLab\Common\Composer\Script\Asset;

use Composer\IO\IOInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class Processor
{
    /** @var Filesystem */
    private $filesystem;
    /** @var IOInterface */
    private $io;

    /**
     * @param Filesystem $filesystem
     * @param IOInterface $io
     *
     * @api
     */
    public function __construct(Filesystem $filesystem, IOInterface $io)
    {
        $this->filesystem = $filesystem;
        $this->io = $io;
    }

    /**
     * @param string $packagePath
     * @param array $map
     * @param bool $isSymlink
     * @param bool $isRelative
     *
     * @throws IOException
     *
     * @api
     */
    public function publish(string $packagePath, array $map, bool $isSymlink, bool $isRelative)
    {
        $packagePath = realpath($packagePath);
        assert('is_dir($packagePath) && is_readable($packagePath)');
        foreach ($map as $from => $to) {
            $origin = realpath(rtrim($packagePath, '/') . '/' . ltrim($from, '/'));
            assert('is_dir($origin) && is_readable($origin)');
            $this->filesystem->remove($to);
            $this->filesystem->mkdir(dirname($to));
            if ($isSymlink) {
                $from = $isRelative
                    ? $this->filesystem->makePathRelative($origin, dirname($to))
                    : $origin;
                $this->publishSymlink($origin, $from, $to);
            } else {
                $this->io->write(sprintf('Installing assets %s as <comment>hard copies</comment>.', $origin));
                $this->filesystem->mirror($origin, $to);
            }
        }
    }

    /**
     * @param string $origin
     * @param string $from
     * @param string $to
     *
     * @throws IOException
     */
    private function publishSymlink(string $origin, string $from, string $to)
    {
        $this->io->write(sprintf('Trying to install assets %s as symbolic links.', $origin));
        try {
            $this->filesystem->symlink($from, $to);
            $this->io->write(sprintf('Assets %s were installed using symbolic links.', $origin));
        } catch (IOException $e) {
            $this->io->write(sprintf(
                'It looks like your system doesn\'t support symbolic links,
                 so assets %s were installed by copying them.',
                $origin
            ));
            $this->filesystem->mirror($origin, $to);
        }
    }
}
