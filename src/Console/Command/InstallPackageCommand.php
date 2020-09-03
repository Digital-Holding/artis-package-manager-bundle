<?php

declare(strict_types=1);

namespace DH\ArtisPackageManagerBundle\Console\Command;

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

        $addTraitCommand = $this->getApplication()->find('artis:add-traits');
        $addTraitCommand->setPackageName($packageName);

        $addTraitInput = new ArrayInput(AddTraitCommand::getDefaultName());
        $addTraitCommand->run($addTraitInput, $output);

        $addPackageConfigCommand = $this->getApplication()->find('artis:add-package-config');
        $addPackageConfigCommand->setPackageName($packageName);

        $addPackageConfigInput = new ArrayInput([AddPackageConfigCommand::getDefaultName()]);
        $addPackageConfigCommand->run($addPackageConfigInput, $output);

        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln('<info>Package has been successfully installed and configured.</info>');
        $outputStyle->newLine();
    }
}
