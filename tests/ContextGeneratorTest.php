<?php

declare(strict_types=1);

namespace Twc\MakerBundle\Tests;

use PHPUnit\Framework\TestCase;
use Twc\MakerBundle\ContextGenerator;

class ContextGeneratorTest extends TestCase
{
    public function testNamespaceWithoutContext()
    {
        $contextGenerator = new ContextGenerator([]);

        $this->assertNull($contextGenerator->getNamespace('', null, 'target'));
    }

    public function testNamespaceWithoutConfig()
    {
        $contextGenerator = new ContextGenerator([]);

        $this->assertNull($contextGenerator->getNamespace('test', 'test', 'target'));
    }

    public function testNamespaceWithoutComponentConfig()
    {
        $contextGenerator = new ContextGenerator([
            ['context' => 'test1', 'target' => 'App'],
        ]);

        $this->assertNull($contextGenerator->getNamespace('test', 'test', 'target'));
    }

    public function testNamespaceWithComponentConfig()
    {
        $contextGenerator = new ContextGenerator([
            'componentTest' => [
                ['context' => 'test1', 'target' => 'App'],
                ['context' => 'test2', 'target' => 'App'],
                ['context' => 'test', 'target' => 'App\Test'],
            ],
        ]);

        $this->assertSame('App\Test', $contextGenerator->getNamespace('componentTest', 'test', 'target'));
        $this->assertNull($contextGenerator->getNamespace('componentTest', 'test3', 'target'));
    }

    public function testNameByContextReturnInitialClass()
    {
        $contextGenerator = new ContextGenerator([]);

        $class = $contextGenerator->classNameByContext(
            'componentTest',
            'Home',
            'test'
        );
        $this->assertSame('Home', $class);
    }

    public function testNameByContextReturnClassContext()
    {
        $contextGenerator = new ContextGenerator([
            'componentTest' => [
                ['context' => 'test1', 'target' => 'App'],
                ['context' => 'test2', 'target' => 'App'],
                ['context' => 'test', 'target' => 'App\Test'],
            ],
        ]);

        $class = $contextGenerator->classNameByContext(
            'componentTest',
            'Home',
            'test'
        );
        $this->assertSame('\App\Test\Home', $class);
    }

    public function testGetDirTemplateByContextWithoutSpecificAction()
    {
        $contextGenerator = new ContextGenerator([
            'controller' => [
                ['context' => 'test', 'target' => 'App', 'dir' => null],
                ['context' => 'test1', 'target' => 'App'],
            ],
        ]);
        $default = 'app/default';
        $result = $contextGenerator->getDirTemplateByContext($default, null);
        $this->assertSame($default, $result);

        $result = $contextGenerator->getDirTemplateByContext($default, 'othercontext');
        $this->assertSame($default, $result);

        $result = $contextGenerator->getDirTemplateByContext($default, 'test');
        $this->assertSame('test', $result);
    }

    public function testGetDirTemplateByContextReturnContextName()
    {
        $contextGenerator = new ContextGenerator([
            'controller' => [
                ['context' => 'test', 'target' => 'App'],
                ['context' => 'test1', 'target' => 'App'],
            ],
        ]);
        $default = 'app/default';

        $result = $contextGenerator->getDirTemplateByContext($default, 'test');
        $this->assertSame('test', $result);
    }

    public function testGetDirTemplateByContextReturnDirName()
    {
        $contextGenerator = new ContextGenerator([
            'controller' => [
                ['context' => 'test', 'target' => 'App'],
                ['context' => 'test1', 'target' => 'App', 'dir' => 'app/dir/'],
            ],
        ]);
        $default = 'app/default';

        $result = $contextGenerator->getDirTemplateByContext($default, 'test1');
        $this->assertSame('app/dir', $result);
    }
}
