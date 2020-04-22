<?php

namespace Twc\MakerBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Twc\MakerBundle\Tests\TwcConsoleTrait;

class MakeTwcControllerTest extends TestCase
{
    use TwcConsoleTrait;

    public function testExecute()
    {
        $config = [
            'controller' => [
                [
                    'context' => 'context.test',
                    'target' => 'Twc\MakerBundle\Tests\Execute',
                    'dir' => 'app',
                ],
            ],
        ];

        $execute = [
            'controller-class' => 'App',
            '--context' => 'context.test',
        ];

        $this->execute('make:twc:controller', $config, $execute);
        $this->assertFileExists(__DIR__ . '/../Execute/AppController.php');
        $this->assertFileExists(__DIR__ . '/../templates/app/index.html.twig');
    }
}
