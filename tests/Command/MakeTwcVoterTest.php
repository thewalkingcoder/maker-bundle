<?php

namespace Twc\MakerBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Twc\MakerBundle\Tests\TwcConsoleTrait;
use Twc\MakerBundle\Tests\TwcMakerKernel;

class MakeTwcVoterTest extends TestCase
{
    use TwcConsoleTrait;

    public function testExecuteWithNewDestination()
    {
        $config = [
            'voter' => [
                ['context' => 'context.test', 'target' => 'Twc\MakerBundle\Tests\Execute']
            ]
        ];

        $execute = [
            'name'      => 'Post',
            '--context' => 'context.test'
        ];

        $this->execute('make:twc:voter', $config, $execute);

        $this->assertFileExists(__DIR__ . '/../Execute/PostVoter.php');
    }

}