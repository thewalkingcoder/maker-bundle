<?php

namespace Twc\MakerBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\MakerBundle\MakerBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Twc\MakerBundle\TwcMakerBundle;

class TwcMakerKernel extends Kernel
{
    use MicroKernelTrait;

    /**
     * @var array
     */
    private $configTwcMaker;

    public function __construct(string $environment, bool $debug, array $configTwcMaker = [])
    {
        parent::__construct($environment, $debug);
        $this->configTwcMaker = $configTwcMaker;
    }

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new MakerBundle(),
            new TwcMakerBundle(),
            new DoctrineBundle(),
            new TwigBundle(),
        ];
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
    }

    protected function configureRouting(RoutingConfigurator $routes)
    {
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $c->loadFromExtension('framework', [
            'secret' => 123,
            'router' => [
                'utf8' => true,
            ],
        ]);

        $c->loadFromExtension('doctrine', [
            'dbal' => ['url' => 'mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7'],
            'orm' => [
                'auto_mapping' => true,
                'mappings' => [
                    'App' => [
                        'type' => 'annotation',
                        'dir' => 'tests/Execute/Entity',
                        'is_bundle' => false,
                        'prefix' => 'Twc\MakerBundle\Tests\Execute\Entity',
                        'alias' => 'App',
                    ],
                ],
            ],
        ]);

        $c->loadFromExtension('twig', [
            'default_path' => 'tests/templates',
            'debug' => false,
            'strict_variables' => false,
            'exception_controller' => null,
        ]);
        $c->loadFromExtension('twc_maker', $this->configTwcMaker);
    }
}
