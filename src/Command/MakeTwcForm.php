<?php

namespace Twc\MakerBundle\Command;

use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Twc\MakerBundle\ContextGenerator;
use Twc\MakerBundle\Support;

class MakeTwcForm extends Command
{
    /**
     * @var ContextGenerator
     */
    private $contextGenerator;

    public function __construct(ContextGenerator $contextGenerator)
    {
        parent::__construct();
        $this->contextGenerator = $contextGenerator;
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates a new form class')
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of the form class')
            ->addArgument('bound-class', InputArgument::OPTIONAL, 'The name of Entity or fully qualified model class name that the new form will be bound to (empty for none)')
            ->addOption('context', 'c', InputOption::VALUE_OPTIONAL, 'your context config to generate on your target')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = $this->getApplication()->find('make:form');
        $name = $input->getArgument('name');
        $boundClass = $input->getArgument('bound-class');
        $context = $input->getOption('context');

        $namespaceContext = $this->contextGenerator->classNameByContext(
            Support::FORM,
            Str::addSuffix($name, 'Type'),
            $context
        );
        $arguments = [
            'command' => 'make:form',
            'name' => $namespaceContext,
            'bound-class' => $boundClass,
        ];

        $greetInput = new ArrayInput($arguments);
        $greetInput->setInteractive(!$input->getOption('no-interaction'));
        $command->run($greetInput, $output);

        return 0;
    }
}
