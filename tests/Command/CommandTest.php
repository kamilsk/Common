<?php

declare(strict_types = 1);

namespace OctoLab\Common\Command;

use OctoLab\Common\TestCase;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class CommandTest extends TestCase
{
    /**
     * @test
     */
    public function setNameTest()
    {
        $command = $this->getCommand();
        self::assertEquals('mock', $command->getName());
        $command->setName('success');
        self::assertEquals('success', $command->getName());
        $command = $this->getCommand('test');
        self::assertEquals('test:mock', $command->getName());
        $command->setName('success');
        self::assertEquals('test:success', $command->getName());
    }

    /**
     * @param string $name
     *
     * @return Command
     */
    private function getCommand(string $name = null): Command
    {
        return new class($name) extends Command
        {
            protected function configure()
            {
                $this->setName('mock');
            }
        };
    }
}
