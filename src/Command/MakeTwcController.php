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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Maker\Common\CanGenerateTestsTrait;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassSource\Model\ClassData;
use Symfony\Bundle\MakerBundle\Util\PhpCompatUtil;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twc\MakerBundle\ContextGenerator;
use Twc\MakerBundle\Support;

final class MakeTwcController extends AbstractMaker
{

    use CanGenerateTestsTrait;

    private bool $isInvokable;
    private ClassData $controllerClassData;
    private bool $usesTwigTemplate;
    private string $twigTemplatePath;

    public function __construct(
        private ContextGenerator $contextGenerator,
        private ?PhpCompatUtil $phpCompatUtil = null,

    ) {
        if (null !== $phpCompatUtil) {
            @trigger_deprecation(
                'symfony/maker-bundle',
                '1.55.0',
                sprintf('Initializing MakeCommand while providing an instance of "%s" is deprecated. The $phpCompatUtil param will be removed in a future version.', PhpCompatUtil::class)
            );
        }
    }

    public static function getCommandName(): string
    {
        return 'make:twc:controller';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a new controller class';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConf): void
    {
        $command
            ->setDescription('Creates a new controller class')
            ->addArgument('controller-class', InputArgument::OPTIONAL, sprintf('Choose a name for your controller class (e.g. <fg=yellow>%sController</>)', Str::asClassName(Str::getRandomTerm())))
            ->addOption('no-template', null, InputOption::VALUE_NONE, 'Use this option to disable template generation')
            ->addOption('invokable', 'i', InputOption::VALUE_NONE, 'Use this option to create an invokable controller')
            ->addOption('context', 'c', InputOption::VALUE_OPTIONAL, 'your context config to generate on your target')
        ;
        $this->configureCommandWithTestsOption($command);
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        $this->usesTwigTemplate = $this->isTwigInstalled() && !$input->getOption('no-template');
        $this->isInvokable = (bool) $input->getOption('invokable');

        $controllerClass = $input->getArgument('controller-class');
        $controllerClassName = \sprintf('Controller\%s', $controllerClass);

        $context = $input->getOption('context');

        $namespaceContext = $this->contextGenerator->classNameByContext(
            Support::CONTROLLER,
            Str::addSuffix($controllerClass, 'Controller'),
            $context
        );

        // If the class name provided is absolute, we do not assume it will live in src/Controller
        // e.g. src/Custom/Location/For/MyController instead of src/Controller/MyController
        if ($isAbsoluteNamespace = '\\' === $controllerClass[0]) {
            $controllerClassName = substr($controllerClass, 1);
        }

        $this->controllerClassData = ClassData::create(
            class: $namespaceContext,
            suffix: 'Controller',
            extendsClass: AbstractController::class,
            useStatements: [
                $this->usesTwigTemplate ? Response::class : JsonResponse::class,
                \Symfony\Component\Routing\Attribute\Route::class,
            ]
        );

        // Again if the class name is absolute, lets not make assumptions about where the twig template
        // should live. E.g. templates/custom/location/for/my_controller.html.twig instead of
        // templates/my/controller.html.twig. We do however remove the root_namespace prefix in either case
        // so we don't end up with templates/app/my/controller.html.twig
        $templateName = $this->controllerClassData->getFullClassName(withoutRootNamespace: true, withoutSuffix: true);

        $dir = $this->contextGenerator->getDirTemplateByContext('template', $context);
        // Convert the twig template name into a file path where it will be generated.
        $this->twigTemplatePath = \sprintf('%s%s', $dir, $this->isInvokable ? '.html.twig' : '/index.html.twig');

        $this->interactSetGenerateTests($input, $io);
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $controllerPath = $generator->generateClassFromClassData($this->controllerClassData, 'controller/Controller.tpl.php', [
            'route_path' => Str::asRoutePath($this->controllerClassData->getClassName(relative: true, withoutSuffix: true)),
            'route_name' => Str::AsRouteName($this->controllerClassData->getClassName(relative: true, withoutSuffix: true)),
            'method_name' => $this->isInvokable ? '__invoke' : 'index',
            'with_template' => $this->usesTwigTemplate,
            'template_name' => $this->twigTemplatePath,
        ], true);

        if ($this->usesTwigTemplate) {
            $generator->generateTemplate(
                $this->twigTemplatePath,
                'controller/twig_template.tpl.php',
                [
                    'controller_path' => $controllerPath,
                    'root_directory' => $generator->getRootDirectory(),
                    'class_name' => $this->controllerClassData->getClassName(),
                ]
            );
        }

        if ($this->shouldGenerateTests()) {
            $testClassData = ClassData::create(
                class: \sprintf('Tests\Controller\%s', $this->controllerClassData->getClassName(relative: true, withoutSuffix: true)),
                suffix: 'ControllerTest',
                extendsClass: WebTestCase::class,
            );

            $generator->generateClassFromClassData($testClassData, 'controller/test/Test.tpl.php', [
                'route_path' => Str::asRoutePath($this->controllerClassData->getClassName(relative: true, withoutSuffix: true)),
            ]);

            if (!class_exists(WebTestCase::class)) {
                $io->caution('You\'ll need to install the `symfony/test-pack` to execute the tests for your new controller.');
            }
        }

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
        $io->text('Next: Open your new controller class and add some pages!');
    }

    public function generate1(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $controllerClass = $input->getArgument('controller-class');
        $context = $input->getOption('context');

        $namespaceContext = $this->contextGenerator->classNameByContext(
            Support::CONTROLLER,
            Str::addSuffix($controllerClass, 'Controller'),
            $context
        );

        $controllerClassNameDetails = $generator->createClassNameDetails(
            $namespaceContext,
            'Controller\\',
            'Controller'
        );

        $noTemplate = $input->getOption('no-template');
        $isInvokable = (bool) $input->getOption('invokable');

        $dirDefault = Str::asFilePath($controllerClassNameDetails->getRelativeNameWithoutSuffix());

        $dirTemplate = $this->contextGenerator->getDirTemplateByContext(
            $dirDefault,
            $context
        );
        $templateName = $dirTemplate . '/index.html.twig';
        $templateExist = file_exists($this->fileManager->getPathForTemplate($templateName));

        $withTemplate = $this->isTwigInstalled() && !$input->getOption('no-template');
        $useStatements = new UseStatementGenerator([
            AbstractController::class,
            $withTemplate ? Response::class : JsonResponse::class,
            Route::class,
        ]);

        $controllerPath = $generator->generateController(
            $controllerClassNameDetails->getFullName(),
            'controller/Controller.tpl.php',
            [
                'use_statements' => $useStatements,
                'route_path' => Str::asRoutePath($controllerClassNameDetails->getRelativeNameWithoutSuffix()),
                'route_name' => Str::asRouteName($controllerClassNameDetails->getRelativeNameWithoutSuffix()),
                'with_template' => $withTemplate,
                'method_name' => $isInvokable ? '__invoke' : 'index',
                'template_name' => $templateName,
            ]
        );

        if ($withTemplate) {
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

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    private function isTwigInstalled(): bool
    {
        return class_exists(TwigBundle::class);
    }
}
