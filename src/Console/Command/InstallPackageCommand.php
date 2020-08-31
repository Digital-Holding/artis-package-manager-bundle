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
            ->addArgument('packageName', InputOption::VALUE_REQUIRED, 'Set package name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packageName = $input->getArgument('packageName');

        $addTraitCommand = $this->getApplication()->find('artis:add-traits');

        passthru('composer require ' . $packageName );

        $addTraitInput = new ArrayInput([$this->parameterBag]);
        $addTraitCommand->run($addTraitInput, $output);

        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln('<info>Package has been successfully installed and configured.</info>');
        $outputStyle->newLine();
    }
}
