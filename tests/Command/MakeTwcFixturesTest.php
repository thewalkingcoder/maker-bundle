<?php

namespace Twc\MakerBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Twc\MakerBundle\Tests\TwcConsoleTrait;

class MakeTwcFixturesTest extends TestCase
{
    use TwcConsoleTrait;

    public function testExecute()
    {
        $config = [
            'fixtures' => [
                ['context' => 'context.test', 'target' => 'Twc\MakerBundle\Tests\Execute'],
            ],
        ];

        $execute = [
            'fixtures-class' => 'PostData',
            '--context' => 'context.test',
        ];

        $this->execute('make:twc:fixtures', $config, $execute);
        $this->assertFileExists(__DIR__ . '/../Execute/PostDataFixtures.php');
    }
}
