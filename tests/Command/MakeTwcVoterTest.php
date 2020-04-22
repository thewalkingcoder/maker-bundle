<?php

namespace Twc\MakerBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Twc\MakerBundle\Tests\TwcConsoleTrait;

class MakeTwcVoterTest extends TestCase
{
    use TwcConsoleTrait;

    public function testExecuteWithNewDestination()
    {
        $config = [
            'voter' => [
                ['context' => 'context.test', 'target' => 'Twc\MakerBundle\Tests\Execute'],
            ],
        ];

        $execute = [
            'name'      => 'Post',
            '--context' => 'context.test',
        ];

        $this->execute('make:twc:voter', $config, $execute);

        $this->assertFileExists(__DIR__ . '/../Execute/PostVoter.php');
    }
}
