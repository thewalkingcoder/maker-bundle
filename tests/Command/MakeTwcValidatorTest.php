<?php

namespace Twc\MakerBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Twc\MakerBundle\Tests\TwcConsoleTrait;
use Twc\MakerBundle\Tests\TwcMakerKernel;

class MakeTwcValidatorTest extends TestCase
{
    use TwcConsoleTrait;

    public function testExecute()
    {
        $config = [
            'validator' => [
                ['context' => 'context.test', 'target' => 'Twc\MakerBundle\Tests\Execute']
            ]
        ];

        $execute = [
            'name'      => 'Post',
            '--context' => 'context.test'
        ];

        $this->execute('make:twc:validator', $config, $execute);
        $this->assertFileExists(__DIR__ . '/../Execute/PostValidator.php');
        $this->assertFileExists(__DIR__ . '/../Execute/Post.php');
    }



}