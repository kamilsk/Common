<?php

namespace OctoLab\Common\Composer\Script\AdminLte;

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
    /** @var Event */
    private $event;
    /** @var Composer */
    private $composer;
    /** @var RootPackageInterface */
    private $package;
    /** @var WritableRepositoryInterface */
    private $localRepository;
    /** @var IOInterface */
    private $io;

    protected function setUp()
    {
        parent::setUp();
        /**
         * @var Composer $composer
         * @var RepositoryManager $repositoryManager
         * @var WritableRepositoryInterface $localRepository
         */
        $this->event = $this->prophesize(Event::class);
        $this->composer = $this->prophesize(Composer::class);
        $this->package = $this->prophesize(RootPackageInterface::class);
        $repositoryManager = $this->prophesize(RepositoryManager::class);
        $this->localRepository = $this->prophesize(WritableRepositoryInterface::class);
        $this->io = $this->prophesize(IOInterface::class);

        $this->event->getComposer()->willReturn($this->composer);
        $this->composer->getPackage()->willReturn($this->package);
        $this->composer->getRepositoryManager()->willReturn($repositoryManager);
        $repositoryManager->getLocalRepository()->willReturn($this->localRepository);
        $this->localRepository->findPackage('almasaeed2010/adminlte', '~2.0')->willReturn(null);
        $this->event->getIO()->willReturn($this->io);
    }

    /**
     * @test
     */
    public function publishSuccess()
    {
        $root = rtrim(substr(__DIR__, 0, strrpos(__DIR__, 'tests')), '\\/') . '/tests/app';
        /**
         * @var PackageInterface $package
         * @var InstallationManager $installationManager
         */
        $package = $this->prophesize(PackageInterface::class);
        $installationManager = $this->prophesize(InstallationManager::class);

        $this->package->getExtra()->willReturn([
            'admin-lte' => [
                'target' => $root . '/web/assets',
                'bootstrap' => true,
                'plugins' => true,
                'symlink' => true,
                'relative' => true,
                'demo' => true,
            ],
        ]);
        $this->localRepository->findPackage('almasaeed2010/adminlte', '~2.0')->willReturn($package);
        $this->composer->getInstallationManager()->willReturn($installationManager);
        $installationManager->getInstallPath($package)->willReturn($root . '/vendor/adminlte');

        Publisher::publish($this->event->reveal());
        self::assertFileExists($root . '/web/assets/adminlte');
        self::assertFileExists($root . '/web/assets/adminlte-bootstrap');
        self::assertFileExists($root . '/web/assets/adminlte-plugins');
        self::assertFileExists($root . '/web/assets/adminlte-demo');
    }

    /**
     * @test
     * @dataProvider invalidConfigurationProvider
     *
     * @param array $extra
     * @param string $exceptionClass
     * @param string $exceptionMessage
     */
    public function publishFailure(array $extra, $exceptionClass, $exceptionMessage)
    {
        $this->package->getExtra()->willReturn($extra);
        $this->setExpectedException($exceptionClass, $exceptionMessage);
        Publisher::publish($this->event->reveal());
    }

    /**
     * @return array[]
     */
    public function invalidConfigurationProvider()
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
}
