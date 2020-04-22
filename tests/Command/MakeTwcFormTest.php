<?php

namespace Twc\MakerBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Twc\MakerBundle\Tests\TwcConsoleTrait;

class MakeTwcFormTest extends TestCase
{
    use TwcConsoleTrait;

    public function testExecute()
    {
        $config = [
            'form' => [
                ['context' => 'context.test', 'target' => 'Twc\MakerBundle\Tests\Execute'],
            ],
        ];

        $execute = [
            'name' => 'Post',
            '--context' => 'context.test',
            '--no-interaction' => true,
        ];

        $this->execute('make:twc:form', $config, $execute);
        $this->assertFileExists(__DIR__ . '/../Execute/PostType.php');
    }
}
