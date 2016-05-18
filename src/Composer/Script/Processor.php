<?php

declare(strict_types = 1);

namespace OctoLab\Common\Composer\Script;

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
     * @param string $targetPath
     * @param string $installPath
     * @param array $map
     * @param bool $isSymlink
     * @param bool $isRelative
     *
     * @throws IOException
     * @throws \InvalidArgumentException
     *
     * @api
     */
    public function publish(string $targetPath, string $installPath, array $map, bool $isSymlink, bool $isRelative)
    {
        $this->filesystem->mkdir($targetPath);
        $targetPath = realpath($targetPath);
        $installPath = realpath($installPath);
        assert('is_dir($targetPath) && is_dir($installPath)');
        foreach ($map as $from => $to) {
            $sourceDir = $installPath . '/' . $from;
            $targetDir = $targetPath . '/' . $to;
            assert('is_dir($sourceDir)');
            $this->filesystem->remove($targetDir);
            if ($isSymlink) {
                $originDir = $isRelative
                    ? $this->filesystem->makePathRelative($sourceDir, $targetPath)
                    : $sourceDir;
                $this->publishSymlink($sourceDir, $originDir, $targetDir, $from);
            } else {
                $this->io->write(sprintf('Installing assets %s as <comment>hard copies</comment>.', $from));
                $this->hardCopy($sourceDir, $targetDir);
            }
        }
    }

    /**
     * @param string $sourceDir
     * @param string $originDir
     * @param string $targetDir
     * @param string $from
     *
     * @throws IOException
     * @throws \InvalidArgumentException
     */
    private function publishSymlink(string $sourceDir, string $originDir, string $targetDir, string $from)
    {
        $this->io->write(sprintf('Trying to install assets %s as symbolic links.', $from));
        try {
            $this->filesystem->symlink($originDir, $targetDir);
            $this->io->write(sprintf('Assets %s were installed using symbolic links.', $from));
        } catch (IOException $e) {
            $this->hardCopy($sourceDir, $targetDir);
            $this->io->write(sprintf(
                'It looks like your system doesn\'t support symbolic links,
                 so assets %s were installed by copying them.',
                $from
            ));
        }
    }

    /**
     * @param string $originDir
     * @param string $targetDir
     *
     * @throws IOException
     * @throws \InvalidArgumentException
     */
    private function hardCopy(string $originDir, string $targetDir)
    {
        $this->filesystem->mkdir($targetDir);
        $this->filesystem->mirror($originDir, $targetDir);
    }
}
