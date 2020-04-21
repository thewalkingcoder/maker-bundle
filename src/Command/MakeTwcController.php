<?php

/*
 * This file is part of the Symfony MakerBundle package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twc\MakerBundle\Command;

use Doctrine\Common\Annotations\Annotation;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Twc\MakerBundle\ContextGenerator;
use Twc\MakerBundle\Support;


final class MakeTwcController extends AbstractMaker
{

    /**
     * @var ContextGenerator
     */
    private $contextGenerator;

    /**
     * @var FileManager
     */
    private $fileManager;

    public function __construct(
        ContextGenerator $contextGenerator,
        FileManager $fileManager
    )
    {
        $this->contextGenerator = $contextGenerator;
        $this->fileManager = $fileManager;
    }

    public static function getCommandName(): string
    {
        return 'make:twc:controller';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConf)
    {
        $command
            ->setDescription('Creates a new controller class')
            ->addArgument('controller-class', InputArgument::OPTIONAL, sprintf('Choose a name for your controller class (e.g. <fg=yellow>%sController</>)', Str::asClassName(Str::getRandomTerm())))
            ->addOption('no-template', null, InputOption::VALUE_NONE, 'Use this option to disable template generation')
            ->addOption('context', 'c', InputOption::VALUE_OPTIONAL, 'your context config to generate on your target')
        ;
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $controllerClass = $input->getArgument('controller-class');
        $context = $input->getOption('context');

        $namespaceContext = $this->contextGenerator->classNameByContext(
            Support::CONTROLLER,
            $controllerClass,
            $context
        );

        $controllerClassNameDetails = $generator->createClassNameDetails(
            $namespaceContext,
            'Controller\\',
            'Controller'
        );

        $noTemplate = $input->getOption('no-template');
        $dirDefault = Str::asFilePath($controllerClassNameDetails->getRelativeNameWithoutSuffix());

        $dirTemplate = $this->contextGenerator->getDirTemplateByContext(
            $dirDefault,
            $context
        );
        $templateName = $dirTemplate.'/index.html.twig';
        $templateExist = file_exists($this->fileManager->getPathForTemplate($templateName));

        $controllerPath = $generator->generateController(
            $controllerClassNameDetails->getFullName(),
            'controller/Controller.tpl.php',
            [
                'route_path' => Str::asRoutePath($controllerClassNameDetails->getRelativeNameWithoutSuffix()),
                'route_name' => Str::asRouteName($controllerClassNameDetails->getRelativeNameWithoutSuffix()),
                'with_template' => $this->isTwigInstalled() && !$noTemplate,
                'template_name' => $templateName,
            ]
        );

        if ($this->isTwigInstalled() && !$noTemplate && !$templateExist) {
            $generator->generateTemplate(
                $templateName,
                'controller/twig_template.tpl.php',
                [
                    'controller_path' => $controllerPath,
                    'root_directory' => $generator->getRootDirectory(),
                    'class_name' => $controllerClassNameDetails->getShortName(),
                ]
            );
        }

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
        $io->text('Next: Open your new controller class and add some pages!');
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(
            Annotation::class,
            'doctrine/annotations'
        );
    }

    private function isTwigInstalled()
    {
        return class_exists(TwigBundle::class);
    }
}
