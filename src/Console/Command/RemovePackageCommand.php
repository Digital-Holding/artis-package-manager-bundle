<?php

declare(strict_types=1);

namespace DH\ArtisPackageManagerBundle\Console\Command;

use DH\ArtisPackageManagerBundle\Console\Command\RemovePackage\RemovePackageConfigCommand;
use DH\ArtisPackageManagerBundle\Console\Command\RemovePackage\RemovePackageRoutingCommand;
use DH\ArtisPackageManagerBundle\Console\Command\RemovePackage\RemoveTraitCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class RemovePackageCommand extends Command
{
    protected static $defaultName = 'artis:package:remove';

    /** @var ParameterBagInterface */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        parent::__construct();

        $this->parameterBag = $parameterBag;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Removing package.')
            ->setHelp('This command allows you to remove package with its configuration...')
            ->addArgument('packageName', InputOption::VALUE_REQUIRED, 'Set package name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packageName = $input->getArgument('packageName');

        if (false !== strpos($packageName, ":")) {
            $packageName = substr($packageName, 0, strpos($packageName, ":"));
        }

        $this->runRemoveTraitCommand($packageName, $output);
        $this->runRemovePackageConfigCommand($packageName, $output);
        $this->runRemovePackageRoutingCommand($packageName, $output);

        passthru('composer remove ' . $packageName);

        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln('<info>Package has been successfully removed.</info>');
        $outputStyle->newLine();
    }

    private function runRemoveTraitCommand(string $packageName, OutputInterface $output): void
    {
        $removeTraitCommand = $this->getApplication()->find('artis:remove-traits');
        $removeTraitCommand->setPackageName($packageName);

        $removeTraitInput = new ArrayInput([RemoveTraitCommand::getDefaultName()]);
        $removeTraitCommand->run($removeTraitInput, $output);
    }

    private function runRemovePackageConfigCommand(string $packageName, OutputInterface $output): void
    {
        $removePackageConfigCommand = $this->getApplication()->find('artis:remove-package-config');
        $removePackageConfigCommand->setPackageName($packageName);

        $removePackageConfigInput = new ArrayInput([RemovePackageConfigCommand::getDefaultName()]);
        $removePackageConfigCommand->run($removePackageConfigInput, $output);
    }

    private function runRemovePackageRoutingCommand(string $packageName, OutputInterface $output): void
    {
        $removePackageRoutingCommand = $this->getApplication()->find('artis:remove-package-routing');
        $removePackageRoutingCommand->setPackageName($packageName);

        $removePackageRoutingInput = new ArrayInput([RemovePackageRoutingCommand::getDefaultName()]);
        $removePackageRoutingCommand->run($removePackageRoutingInput, $output);
    }
}
