<?php

namespace Twc\MakerBundle\Tests;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\MakerBundle\MakerBundle;
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
            new TwcMakerBundle()
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
        $c->loadFromExtension('twc_maker', $this->configTwcMaker);
    }
}