<?php

declare(strict_types = 1);

namespace OctoLab\Common\Asset;

use Symfony\Component\Asset\Context\ContextInterface;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class AdminLtePackage extends PathPackage
{
    /** @var array */
    private $map;

    /**
     * @param string $basePath
     * @param VersionStrategyInterface $versionStrategy
     * @param ContextInterface|null $context
     */
    public function __construct(
        string $basePath,
        VersionStrategyInterface $versionStrategy,
        ContextInterface $context = null
    ) {
        parent::__construct($basePath, $versionStrategy, $context);
        $this->map = [
            ':bootstrap' => 'bootstrap',
            ':demo' => '',
            ':dist' => 'dist',
            ':plugins' => 'plugins',
        ];
    }

    /**
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function getBootstrapMap(): array
    {
        return ['/bootstrap' => $this->getUrl(':bootstrap')];
    }

    /**
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function getDemoMap(): array
    {
        return ['/' => $this->getUrl(':demo')];
    }

    /**
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function getDistMap(): array
    {
        return ['/dist' => $this->getUrl(':dist')];
    }

    /**
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function getPluginsMap(): array
    {
        return ['/plugins' => $this->getUrl(':plugins')];
    }

    /**
     * @param string $path
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function getUrl($path): string
    {
        assert('is_string($path)');
        if (strpos($path, ':') === 0) {
            $key = substr($path, 0, strpos($path, '/') ?: strlen($path));
            $path = $key === $path ? '' : substr($path, strlen($key));
        } else {
            $key = ':dist';
        }
        if (!isset($this->map[$key])) {
            throw new \InvalidArgumentException(sprintf('No path "%s" for subpackage "%s".', $path, $key));
        }
        return parent::getUrl(substr($key, 1) . $path);
    }
}
