<?php

declare(strict_types = 1);

namespace OctoLab\Common\Composer\Script\AdminLte;

use Composer\IO\IOInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class Processor
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
        $targetPath = rtrim($targetPath, '/');
        $installPath = rtrim($installPath, '/');
        $this->filesystem->mkdir($targetPath, 0777);
        foreach ($map as $from => $to) {
            $targetDir = realpath($targetPath) . '/' . $to;
            $sourceDir = realpath($installPath) . '/' . $from;
            $this->filesystem->remove($targetDir);
            if ($isSymlink) {
                $originDir = $isRelative
                    ? $this->filesystem->makePathRelative($sourceDir, realpath($targetPath))
                    : $sourceDir;
                $this->publishSymlink($from, $originDir, $targetDir, $sourceDir);
            } else {
                $this->io->write(sprintf('Installing AdminLTE %s assets as <comment>hard copies</comment>.', $from));
                $this->hardCopy($sourceDir, $targetDir);
            }
        }
    }

    /**
     * @param string $from
     * @param string $originDir
     * @param string $targetDir
     * @param string $sourceDir
     *
     * @throws IOException
     * @throws \InvalidArgumentException
     */
    private function publishSymlink(string $from, string $originDir, string $targetDir, string $sourceDir)
    {
        $this->io->write(sprintf('Trying to install AdminLTE %s assets as symbolic links.', $from));
        try {
            $this->filesystem->symlink($originDir, $targetDir);
            $this->io->write(sprintf('The AdminLTE %s assets were installed using symbolic links.', $from));
        } catch (IOException $e) {
            $this->hardCopy($sourceDir, $targetDir);
            $this->io->write(sprintf(
                'It looks like your system doesn\'t support symbolic links,
                        so the AdminLTE %s assets were installed by copying them.',
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
        $this->filesystem->mkdir($targetDir, 0777);
        $this->filesystem->mirror($originDir, $targetDir);
    }
}
