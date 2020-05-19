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

class MakeTwcVoter extends Command
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
            ->setDescription('Creates a new security voter class')
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of the security voter class (e.g. <fg=yellow>BlogPostVoter</>)')
            ->addOption('context', 'c', InputOption::VALUE_OPTIONAL, 'your context config to generate on your target')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = $this->getApplication()->find('make:voter');
        $name = $input->getArgument('name');
        $context = $input->getOption('context');

        $namespaceContext = $this->contextGenerator->classNameByContext(
            Support::VOTER,
            Str::addSuffix($name, 'Voter'),
            $context
        );

        $arguments = [
            'command' => 'make:voter',
            'name' => $namespaceContext,
        ];

        $greetInput = new ArrayInput($arguments);
        $greetInput->setInteractive(!$input->getOption('no-interaction'));
        $command->run($greetInput, $output);

        return 0;
    }
}
