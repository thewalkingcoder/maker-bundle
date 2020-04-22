<?php

namespace Twc\MakerBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Twc\MakerBundle\Tests\TwcConsoleTrait;

class MakeTwcEntityTest extends TestCase
{
    use TwcConsoleTrait;

    public function testExecute()
    {
        $config = [
            'entity' => [
                [
                    'context'           => 'context.test',
                    'target_entity'     => 'Twc\MakerBundle\Tests\Execute\Entity',
                    'target_repository' => 'Twc\MakerBundle\Tests\Execute\Repository',
                ],
            ],
        ];

        $execute = [
            'name'      => 'Post',
            '--context' => 'context.test',
        ];

        $this->execute('make:twc:entity', $config, $execute);
        $this->assertFileExists(__DIR__ . '/../Execute/Entity/Post.php');
        $this->assertFileExists(__DIR__ . '/../Execute/Repository/PostRepository.php');
    }
}
