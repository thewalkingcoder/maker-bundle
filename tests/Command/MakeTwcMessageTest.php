<?php

namespace Twc\MakerBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Twc\MakerBundle\Tests\TwcConsoleTrait;

class MakeTwcMessageTest extends TestCase
{
    use TwcConsoleTrait;

    public function testExecute()
    {
        $config = [
            'message' => [
                ['context' => 'context.test', 'target' => 'Twc\MakerBundle\Tests\Execute'],
            ],
        ];

        $execute = [
            'name'      => 'CreateNewPost',
            '--context' => 'context.test',
        ];

        $this->execute('make:twc:message', $config, $execute);
        $this->assertFileExists(__DIR__ . '/../Execute/CreateNewPost.php');
        $this->assertFileExists(__DIR__ . '/../Execute/CreateNewPostHandler.php');
    }
}
