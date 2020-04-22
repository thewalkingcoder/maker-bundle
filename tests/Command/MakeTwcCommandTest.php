<?php

namespace Twc\MakerBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Twc\MakerBundle\Tests\TwcConsoleTrait;

class MakeTwcCommandTest extends TestCase
{
    use TwcConsoleTrait;

    public function testExecute()
    {
        $config = [
            'command' => [
                ['context' => 'context.test', 'target' => 'Twc\MakerBundle\Tests\Execute'],
            ],
        ];

        $execute = [
            'name' => 'PostCommand',
            '--context' => 'context.test',
        ];

        $this->execute('make:twc:command', $config, $execute);
        $this->assertFileExists(__DIR__ . '/../Execute/PostCommand.php');
    }
}
