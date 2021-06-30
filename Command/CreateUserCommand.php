<?php

namespace OAuth2\ServerBundle\Command;

use Exception;
use OAuth2\ServerBundle\User\OAuth2UserProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateUserCommand
 */
class CreateUserCommand extends Command
{
    /**
     * @var OAuth2UserProvider
     */
    protected OAuth2UserProvider $userProvider;

    /**
     * CreateUserCommand constructor.
     *
     * @param OAuth2UserProvider $userProvider
     * @param string|null        $name
     */
    public function __construct(OAuth2UserProvider $userProvider, ?string $name = null)
    {
        parent::__construct($name);
        $this->userProvider = $userProvider;
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('OAuth2:CreateUser')
            ->setDescription('Create a basic OAuth2 user')
            ->addArgument('username', InputArgument::REQUIRED, 'The users unique username')
            ->addArgument('password', InputArgument::REQUIRED, 'The users password (plaintext)')
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
            $this->userProvider->createUser(
                $input->getArgument('username'),
                $input->getArgument('password')
            );
        } catch (Exception $exception) {
            $output->writeln(
                '<fg=red>Unable to create user ' . $input->getArgument('username') . '</fg=red>'
            );

            return 1;
        }

        $output->writeln('<fg=green>User ' . $input->getArgument('username') . ' created</fg=green>');

	return 0;
    }
}
