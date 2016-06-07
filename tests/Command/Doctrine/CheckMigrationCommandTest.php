<?php

declare(strict_types = 1);

namespace OctoLab\Common\Command\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\MigrationException;
use Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Helper\ConfigurationHelper;
use OctoLab\Common\Doctrine\Migration\FileBasedMigration;
use OctoLab\Common\TestCase;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class CheckMigrationCommandTest extends TestCase
{
    /** @var Configuration */
    private $configuration;

    /**
     * @test
     */
    public function execute()
    {
        $command = new CheckMigrationCommand('test');
        $command->setHelperSet(new HelperSet());
        $command->getHelperSet()->set(
            new ConfigurationHelper(null, $this->configuration->reveal()),
            'configuration'
        );

        $migration = $this->getMigrationPath('2/ISSUE-29/upgrade.sql');
        $input = new ArgvInput(
            [
                $command->getName(),
                $migration,
            ],
            $command->getDefinition()
        );
        $output = new BufferedOutput();
        $command->run($input, $output);
        $needle = <<<EOF
Migration %s contains
1. CREATE TABLE IF NOT EXISTS test ( id INT, title VARCHAR(8) NOT NULL, PRIMARY KEY (id) )
EOF;
        self::assertContains(sprintf($needle, $migration), $output->fetch());

        $migration = $this->getMigrationPath('2/ISSUE-29/downgrade.sql');
        $input = new ArgvInput(
            [
                $command->getName(),
                $migration,
            ],
            $command->getDefinition()
        );
        $command->run($input, $output);
        self::assertContains(sprintf('Migration %s is empty', $migration), $output->fetch());

        $migration = '20160320120000';
        $input = new ArgvInput(
            [
                $command->getName(),
                $migration,
            ],
            $command->getDefinition()
        );
        $command->run($input, $output);
        $needle = <<<EOF
Upgrade by migration %s contains
1. CREATE TABLE IF NOT EXISTS test ( id INT, title VARCHAR(8) NOT NULL, PRIMARY KEY (id) )
Downgrade by migration %s is empty
EOF;
        self::assertContains(sprintf($needle, $migration, $migration), $output->fetch());

        $migration = Version20160320120000::class;
        $input = new ArgvInput(
            [
                $command->getName(),
                $migration,
            ],
            $command->getDefinition()
        );
        $command->run($input, $output);
        self::assertContains(
            sprintf($needle, $migration, $migration),
            $output->fetch()
        );

        $migration = '20160320000000';
        $input = new ArgvInput(
            [
                $command->getName(),
                $migration,
            ],
            $command->getDefinition()
        );
        try {
            $command->run($input, $output);
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
            self::assertContains(
                sprintf(
                    'Migration must be an instance of %s. Use "--dry-run" option of %s to see its\' content instead.',
                    FileBasedMigration::class,
                    MigrateCommand::class
                ),
                $e->getMessage()
            );
        }

        try {
            $input = new ArgvInput(
                [
                    $command->getName(),
                    'Unknown\\MigrationClass',
                ],
                $command->getDefinition()
            );
            $command->run($input, $output);
            self::fail(sprintf('%s exception expected.', MigrationException::class));
        } catch (MigrationException $e) {
            self::assertContains('Could not find migration version Unknown\\MigrationClass', $e->getMessage());
        }
    }

    protected function setUp()
    {
        parent::setUp();
        $this->configuration = $this->prophesize(Configuration::class);
        $this->configuration->getVersion('20160320120000')->willReturn(new class()
        {
            public function getMigration(): Version20160320120000
            {
                return (new \ReflectionClass(Version20160320120000::class))->newInstanceWithoutConstructor();
            }
        });
        $this->configuration->getVersion('20160320000000')->will(function (array $args) {
            $configuration = new Configuration(new Connection([
                'driver' => 'pdo_mysql',
                'host' => 'localhost',
                'port' => 3306,
                'dbname' => 'database',
                'user' => 'user',
                'password' => 'pass',
            ], new Driver()));
            $configuration->setMigrationsDirectory(__DIR__);
            $configuration->setMigrationsNamespace(__NAMESPACE__);
            $configuration->setMigrationsTableName('migration');
            return $configuration->getVersion($args[0]);
        });
        $this->configuration->getMigrationsNamespace()->willReturn(__NAMESPACE__);
        $this->configuration->getVersion('Unknown\\MigrationClass')->will(function (array $args) {
            throw MigrationException::unknownMigrationVersion($args[0]);
        });
    }
}
