<?php

declare(strict_types = 1);

namespace OctoLab\Common\Command\Doctrine;

use OctoLab\Common\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class GenerateIndexNameCommandTest extends TestCase
{
    /**
     * @test
     */
    public function execute()
    {
        $command = new GenerateIndexNameCommand('test');
        $input = new ArgvInput(
            [
                $command->getName(),
                '--type=idx',
                '--table=test',
                '--columns=id,title',
            ],
            $command->getDefinition()
        );
        $output = new BufferedOutput();
        $command->run($input, $output);
        self::assertContains('IDX_D87F7E0CBF3967502B36786B', $output->fetch());
        $input = new ArgvInput(
            [
                $command->getName(),
                '--type=uniq',
                '--table=test',
                '--columns=title',
            ],
            $command->getDefinition()
        );
        $command->run($input, $output);
        self::assertContains('UNIQ_D87F7E0C2B36786B', $output->fetch());
        $input = new ArgvInput(
            [
                $command->getName(),
                '--type=fk',
                '--table=test',
                '--columns=rel_id',
            ],
            $command->getDefinition()
        );
        $command->run($input, $output);
        self::assertContains('FK_D87F7E0C4E0AA1CD', $output->fetch());
        try {
            $input = new ArgvInput(
                [
                    $command->getName(),
                    '--type=unknown',
                    '--table=test',
                    '--columns=rel_id',
                ],
                $command->getDefinition()
            );
            $command->run($input, $output);
            self::fail(sprintf('%s exception expected.', \InvalidArgumentException::class));
        } catch (\InvalidArgumentException $e) {
            self::assertContains('Unknown type "unknown", available types are "uniq","fk","idx".', $e->getMessage());
        }
    }
}
