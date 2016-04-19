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
     *
     * @quality:method [B]
     */
    public function publish($targetPath, $installPath, array $map, $isSymlink, $isRelative)
    {
        $targetPath = rtrim($targetPath, '/');
        $installPath = rtrim($installPath, '/');
        $this->filesystem->mkdir($targetPath, 0777);
        foreach ($map as $from => $to) {
            $targetDir = realpath($targetPath) . '/' . $to;
            $sourceDir = realpath($installPath) . '/' . $from;
            $this->filesystem->remove($targetDir);
            if ($isSymlink) {
                $this->io->write(sprintf('Trying to install AdminLTE %s assets as symbolic links.', $from));
                $originDir = $sourceDir;
                if ($isRelative) {
                    $originDir = $this->filesystem->makePathRelative($sourceDir, realpath($targetPath));
                }
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
            } else {
                $this->io->write(sprintf('Installing AdminLTE %s assets as <comment>hard copies</comment>.', $from));
                $this->hardCopy($sourceDir, $targetDir);
            }
        }
    }

    /**
     * @param string $originDir
     * @param string $targetDir
     *
     * @throws IOException
     * @throws \InvalidArgumentException
     */
    private function hardCopy($originDir, $targetDir)
    {
        $this->filesystem->mkdir($targetDir, 0777);
        $this->filesystem->mirror($originDir, $targetDir);
    }
}
