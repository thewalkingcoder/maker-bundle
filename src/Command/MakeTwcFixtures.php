<?php

namespace Twc\MakerBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Twc\MakerBundle\ContextGenerator;
use Twc\MakerBundle\Support;

class MakeTwcFixtures extends Command
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
            ->setDescription('Creates a new class to load Doctrine fixtures')
            ->addArgument('fixtures-class', InputArgument::OPTIONAL,
                'The class name of the fixtures to create (e.g. <fg=yellow>AppFixtures</>)')
            ->addOption('context', 'c', InputOption::VALUE_OPTIONAL, 'your context config to generate on your target');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = $this->getApplication()->find('make:fixtures');
        $name = $input->getArgument('fixtures-class');
        $context = $input->getOption('context');

        $namespaceContext = $this->contextGenerator->classNameByContext(
            Support::FIXTURES,
            str_replace('Fixtures', '', $name) . 'Fixtures',
            $context
        );

        $arguments = [
            'command' => 'make:fixtures',
            'fixtures-class' => $namespaceContext,
        ];

        $greetInput = new ArrayInput($arguments);
        $greetInput->setInteractive(!$input->getOption('no-interaction'));
        $command->run($greetInput, $output);

        return 0;
    }
}
