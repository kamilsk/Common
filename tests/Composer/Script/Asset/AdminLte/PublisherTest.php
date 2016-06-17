<?php

declare(strict_types = 1);

namespace OctoLab\Common\Composer\Script\Asset\AdminLte;

use Composer\Composer;
use Composer\Installer\InstallationManager;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackageInterface;
use Composer\Repository\RepositoryManager;
use Composer\Repository\WritableRepositoryInterface;
use Composer\Script\Event;
use OctoLab\Common\TestCase;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class PublisherTest extends TestCase
{
    /** @var Composer */
    private $composer;
    /** @var Event */
    private $event;
    /** @var WritableRepositoryInterface */
    private $localRepository;
    /** @var RootPackageInterface */
    private $package;

    /**
     * @return array
     */
    public function invalidConfigurationProvider(): array
    {
        return [
            'no extra' => [
                [],
                \InvalidArgumentException::class,
                'The AdminLTE installer needs to be configured through the extra.admin-lte setting.',
            ],
            'no target' => [
                [
                    'admin-lte' => [],
                ],
                \InvalidArgumentException::class,
                'The extra.admin-lte must contains target path.',
            ],
            'no package' => [
                [
                    'admin-lte' => [
                        'target' => 'web/assets',
                    ],
                ],
                \RuntimeException::class,
                'The AdminLTE package not found.',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider invalidConfigurationProvider
     *
     * @param array $extra
     * @param string $exceptionClass
     * @param string $exceptionMessage
     */
    public function publishFailure(array $extra, string $exceptionClass, string $exceptionMessage)
    {
        $this->package->getExtra()->willReturn($extra);
        $this->setExpectedException($exceptionClass, $exceptionMessage);
        Publisher::publish($this->event->reveal());
    }

    /**
     * @test
     */
    public function publishSuccess()
    {
        $appDir = $this->getAppDir();
        /**
         * @var PackageInterface $package
         * @var InstallationManager $installationManager
         */
        $package = $this->prophesize(PackageInterface::class);
        $installationManager = $this->prophesize(InstallationManager::class);

        $this->package->getExtra()->willReturn([
            'admin-lte' => [
                'target' => $appDir . '/web/assets',
                'bootstrap' => true,
                'plugins' => true,
                'demo' => true,
                'symlink' => true,
                'relative' => true,
            ],
        ]);
        $this->localRepository->findPackage('almasaeed2010/adminlte', '~2.0')->willReturn($package);
        $this->composer->getInstallationManager()->willReturn($installationManager);
        $installationManager->getInstallPath($package)->willReturn($appDir . '/vendor/adminlte');

        Publisher::publish($this->event->reveal());
        self::assertFileExists($appDir . '/web/assets');
        self::assertFileExists($appDir . '/web/assets/dist');
        self::assertFileExists($appDir . '/web/assets/bootstrap');
        self::assertFileExists($appDir . '/web/assets/plugins');
        self::assertFileExists($appDir . '/web/assets/demo');
    }

    protected function setUp()
    {
        parent::setUp();
        /**
         * @var RepositoryManager $repositoryManager
         */
        $this->event = $this->prophesize(Event::class);
        $this->composer = $this->prophesize(Composer::class);
        $this->package = $this->prophesize(RootPackageInterface::class);
        $repositoryManager = $this->prophesize(RepositoryManager::class);
        $this->localRepository = $this->prophesize(WritableRepositoryInterface::class);
        $io = $this->prophesize(IOInterface::class);

        $this->event->getComposer()->willReturn($this->composer);
        $this->composer->getPackage()->willReturn($this->package);
        $this->composer->getRepositoryManager()->willReturn($repositoryManager);
        $repositoryManager->getLocalRepository()->willReturn($this->localRepository);
        $this->localRepository->findPackage('almasaeed2010/adminlte', '~2.0')->willReturn(null);
        $this->event->getIO()->willReturn($io);
    }
}
