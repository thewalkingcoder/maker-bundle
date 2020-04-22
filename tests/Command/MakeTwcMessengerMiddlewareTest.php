<?php

namespace Twc\MakerBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Twc\MakerBundle\Tests\TwcConsoleTrait;
use Twc\MakerBundle\Tests\TwcMakerKernel;

class MakeTwcMessengerMiddlewareTest extends TestCase
{
    use TwcConsoleTrait;

    public function testExecute()
    {
        $config = [
            'messenger_middleware' => [
                ['context' => 'context.test', 'target' => 'Twc\MakerBundle\Tests\Execute']
            ]
        ];

        $execute = [
            'name'      => 'Notification',
            '--context' => 'context.test'
        ];

        $this->execute('make:twc:messenger-middleware', $config, $execute);
        $this->assertFileExists(__DIR__ . '/../Execute/NotificationMiddleware.php');
    }



}