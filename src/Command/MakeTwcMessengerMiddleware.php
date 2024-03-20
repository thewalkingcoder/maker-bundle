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

class MakeTwcMessengerMiddleware extends Command
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

    protected function configure(): void
    {
        $this
            ->setDescription('Creates a new messenger middleware')
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of the middleware class (e.g. <fg=yellow>CustomMiddleware</>)')
            ->addOption('context', 'c', InputOption::VALUE_OPTIONAL, 'your context config to generate on your target')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = $this->getApplication()->find('make:messenger-middleware');
        $name = $input->getArgument('name');
        $context = $input->getOption('context');

        $namespaceContext = $this->contextGenerator->classNameByContext(
            Support::MESSENGER_MIDDLEWARE,
            Str::addSuffix($name, 'Middleware'),
            $context
        );

        $arguments = [
            'command' => 'make:messenger-middleware',
            'name' => $namespaceContext,
        ];

        $greetInput = new ArrayInput($arguments);
        $greetInput->setInteractive(!$input->getOption('no-interaction'));
        $command->run($greetInput, $output);

        return 0;
    }
}
