<?php

declare(strict_types=1);

namespace DH\ArtisPackageManagerBundle\Console\Command;

use DH\ArtisPackageManagerBundle\Console\Command\InstallPackage\AddInterfaceCommand;
use DH\ArtisPackageManagerBundle\Console\Command\InstallPackage\AddPackageConfigCommand;
use DH\ArtisPackageManagerBundle\Console\Command\InstallPackage\AddPackageRoutingCommand;
use DH\ArtisPackageManagerBundle\Console\Command\InstallPackage\AddTraitCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class InstallPackageCommand extends Command
{
    protected static $defaultName = 'artis:package:install';

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
            ->setDescription('Installing package.')
            ->setHelp('This command allows you to install and set up package...')
            ->addArgument('packageName', InputOption::VALUE_REQUIRED, 'Set package name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packageName = $input->getArgument('packageName');

        passthru('composer require ' . $packageName);

        if (false !== strpos($packageName, ":")) {
            $packageName = substr($packageName, 0, strpos($packageName, ":"));
        }

        $projectDir = $this->parameterBag->get('kernel.project_dir');

        $configPath = $projectDir . '/vendor/' . $packageName . '/src/Resources/config/artis_package_manager_config.json';

        $this->runAddTraitCommand($packageName, $output, $configPath);
        $this->runAddInterfaceCommand($packageName, $output, $configPath);
        $this->runAddPackageConfigCommand($packageName, $output, $configPath, $projectDir);
        $this->runAddPackageRoutingCommand($packageName, $output, $configPath, $projectDir);

        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln('<info>Package has been successfully installed and configured.</info>');
        $outputStyle->newLine();
    }

    private function runAddTraitCommand(string $packageName, OutputInterface $output, string $configPath): void
    {
        $addTraitCommand = $this->getApplication()->find('artis:add-traits');
        $addTraitCommand->setPackageName($packageName);

        $addTraitCommand->setConfigPath($configPath);

        $addTraitInput = new ArrayInput([AddTraitCommand::getDefaultName()]);
        $addTraitCommand->run($addTraitInput, $output);
    }

    private function runAddPackageConfigCommand(string $packageName, OutputInterface $output, string $configPath, string $projectDir): void
    {
        $addPackageConfigCommand = $this->getApplication()->find('artis:add-package-config');
        $addPackageConfigCommand->setPackageName($packageName);
        $addPackageConfigCommand->setConfigPath($configPath);
        $addPackageConfigCommand->setProjectDir($projectDir);

        $addPackageConfigInput = new ArrayInput([AddPackageConfigCommand::getDefaultName()]);
        $addPackageConfigCommand->run($addPackageConfigInput, $output);
    }

    private function runAddPackageRoutingCommand(string $packageName, OutputInterface $output, string $configPath, string $projectDir): void
    {
        $addPackageRoutingCommand = $this->getApplication()->find('artis:add-package-routing');
        $addPackageRoutingCommand->setPackageName($packageName);
        $addPackageRoutingCommand->setConfigPath($configPath);
        $addPackageRoutingCommand->setProjectDir($projectDir);

        $addPackageRoutingInput = new ArrayInput([AddPackageRoutingCommand::getDefaultName()]);
        $addPackageRoutingCommand->run($addPackageRoutingInput, $output);
    }

    private function runAddInterfaceCommand(string $packageName, OutputInterface $output, string $configPath): void
    {
        $addInterfaceCommand = $this->getApplication()->find('artis:add-interfaces');
        $addInterfaceCommand->setPackageName($packageName);
        $addInterfaceCommand->setConfigPath($configPath);

        $addInterfaceInput = new ArrayInput([AddInterfaceCommand::getDefaultName()]);
        $addInterfaceCommand->run($addInterfaceInput, $output);
    }
}
