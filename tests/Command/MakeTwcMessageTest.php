<?php

namespace Twc\MakerBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Twc\MakerBundle\Tests\TwcConsoleTrait;
use Twc\MakerBundle\Tests\TwcMakerKernel;

class MakeTwcMessageTest extends TestCase
{
    use TwcConsoleTrait;

    public function testExecute()
    {
        $config = [
            'message' => [
                ['context' => 'context.test', 'target' => 'Twc\MakerBundle\Tests\Execute']
            ]
        ];

        $execute = [
            'name'      => 'CreateNewPost',
            '--context' => 'context.test'
        ];

        $this->execute('make:twc:message', $config, $execute);
        $this->assertFileExists(__DIR__ . '/../Execute/CreateNewPost.php');
        $this->assertFileExists(__DIR__ . '/../Execute/CreateNewPostHandler.php');
    }



}