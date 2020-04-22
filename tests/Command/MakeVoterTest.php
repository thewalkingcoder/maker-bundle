<?php

namespace Twc\MakerBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Twc\MakerBundle\Tests\TwcConsoleTrait;

class MakeVoterTest extends TestCase
{
    use TwcConsoleTrait;

    public function testExecuteWithNew()
    {
        $config = [
            'voter' => [
                ['context' => 'context.test', 'target' => 'Twc\MakerBundle\Tests']
            ]
        ];

        $execute = [
            'name'      => 'Post',
            '--context' => 'context.test'
        ];

        $this->execute(
            'make:twc:voter',
            $config,
            $execute
        );

        $this->assertFileExists(__DIR__ . '/../PostVoter.php');
    }

    public function tearDown()
    {
        unlink(__DIR__ . '/../PostVoter.php');
    }

}