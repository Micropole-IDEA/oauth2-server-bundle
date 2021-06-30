<?php

namespace OAuth2\ServerBundle\Command;

use Exception;
use OAuth2\ServerBundle\Manager\ScopeManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateScopeCommand
 */
class CreateScopeCommand extends Command
{
    /**
     * @var ScopeManager
     */
    protected ScopeManager $scopeManager;

    /**
     * CreateScopeCommand constructor.
     *
     * @param ScopeManager $scopeManager
     * @param string|null  $name
     */
    public function __construct(ScopeManager $scopeManager, ?string $name = null)
    {
        parent::__construct($name);
        $this->scopeManager = $scopeManager;
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('OAuth2:CreateScope')
            ->setDescription('Create a scope for use in OAuth2')
            ->addArgument('scope', InputArgument::REQUIRED, 'The scope key/name')
            ->addArgument(
                'description',
                InputArgument::REQUIRED,
                'The scope description used on authorization screen'
            )
        ;
    }

    /**
     * execute
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->scopeManager->createScope(
                $input->getArgument('scope'),
                $input->getArgument('description')
            );
        } catch (Exception $exception) {
            $output->writeln(
                '<fg=red>Unable to create scope ' . $input->getArgument('scope') . '</fg=red>'
            );

            return 1;
        }

        $output->writeln('<fg=green>Scope ' . $input->getArgument('scope') . ' created</fg=green>');

	return 0;
    }
}
