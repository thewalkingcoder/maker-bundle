<?php

namespace Twc\MakerBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

trait TwcConsoleTrait
{

    public function execute(string $commandName, array $config, array $executeOptions)
    {
        $kernel = new TwcMakerKernel('dev', true, $config);
        $application = new Application($kernel);
        $command = $application->find($commandName);

        $commandTester = new CommandTester($command);
        $commandTester->execute($executeOptions, ['interactive' => false]);
    }

    public function tearDown()
    {

        $finder = new Finder();
        $finder->files()->name('*.php')->in(__DIR__ . '/Execute');

        $removeFiles[] = __DIR__ . '/../var';
        $removeFiles[] = __DIR__ . '/templates';
        foreach ($finder as $file) {
            $removeFiles [] = $file->getRealPath();
        }

        $fileSystem = new Filesystem();
        $fileSystem->remove($removeFiles);


    }
}