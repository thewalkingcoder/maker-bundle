<?php

namespace Twc\MakerBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

trait TwcConsoleTrait
{
    public function execute(string $commandName, array $config, array $executeOptions)
    {
        $kernel = new TwcMakerKernel('dev', true, $config);
        $application = new Application($kernel);
        $command = $application->find($commandName);

        $commandTester = new CommandTester($command);
        $commandTester->execute($executeOptions);
    }
}