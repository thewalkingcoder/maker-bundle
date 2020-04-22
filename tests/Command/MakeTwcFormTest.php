<?php

namespace Twc\MakerBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Twc\MakerBundle\Tests\TwcConsoleTrait;
use Twc\MakerBundle\Tests\TwcMakerKernel;

class MakeTwcFormTest extends TestCase
{
    use TwcConsoleTrait;

    public function testExecute()
    {
        $config = [
            'form' => [
                ['context' => 'context.test', 'target' => 'Twc\MakerBundle\Tests\Execute']
            ]
        ];

        $execute = [
            'name'      => 'Post',
            '--context' => 'context.test',
            '--no-interaction' => true
        ];

        $this->execute('make:twc:form', $config, $execute);
        $this->assertFileExists(__DIR__ . '/../Execute/PostType.php');
    }



}